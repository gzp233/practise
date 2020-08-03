<?php

namespace App\Http\Controllers\Storage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Storage\Goods;
use App\Models\Storage\CheckTag;
use App\Models\Storage\GoodsRecord;
use App\Models\Base\Product;
use Illuminate\Support\Facades\DB;
use Excel;
use App\Models\Storage\Frost;

class DiffCheckController extends Controller
{
    protected $rules = [];

    /**
     * Create a new GoodsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    /**
     * 分页获取库存信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $query = [];
        $limit = !empty($params['limit']) ? $params['limit'] : 20;
        if (!empty($params['PRODCHINM'])) $query[] = ['p.PRODCHINM', 'like', '%' . $params['PRODCHINM'] . '%'];
        if (!empty($params['state_name'])) $query[] = ['g.state_name', 'like', '%' . $params['state_name']];
        if (!empty($params['CHARG'])) $query[] = ['g.CHARG', 'like', '%' . $params['CHARG']];
        if (!empty($params['stock_no'])) $query[] = ['g.stock_no', 'like', '%' . $params['stock_no']];
        if (!empty($params['NewPRODUCTCD'])) $query[] = ['p.NewPRODUCTCD', 'like', '%' . $params['NewPRODUCTCD']];
        if (!empty($params['PRODUCTCD'])) $query[] = ['p.PRODUCTCD', 'like', '%' . $params['PRODUCTCD']];
        $data = DB::table('goods as g')
            ->leftJoin('product as p', 'g.product_id', '=', 'p.id')
            ->select('p.id', DB::raw('sum(g.number) as total'), 'p.PRODCHINM', 'p.PRODUCTCD', 'p.NewPRODUCTCD', DB::raw('group_concat(g.id) as goods_ids'))
            ->where($query)
            ->groupBy('product_id');

        $data = $data->paginate($limit);

        return sendData(200, '', $data);
    }

    public function goodsList(Request $request)
    {
        $all = $request->all();
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = $q = [];
        if ($request->get('stock_no')) $params[] = ['stock_no', 'like', $request->get('stock_no') . '%'];
        $result = DB::table('diff_check_tag')->where('state', '0')->where('status','0')->where('diff_code', $all['id'])->where($params);
//        $result = DB::table('diff_check_tag')->where('status', '0')->where('diff_code', $all['id'])->where($params);
        $data = $result->paginate($limit);

        return sendData(200, '请求成功', $data);
    }

    public function getByNo(Request $request)
    {
        $all = $request->all();
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $query = [];
        if ($request->get('batches')) $query[] = ['batches', '=', $request->get('batches')];
        if ($request->get('is_diff')) $query[] = ['is_diff', '=', $request->get('is_diff')];
        $query[] = ['batches', '!=', ''];
        $builder = DB::table('check_tag')
            ->select('batches','is_diff',DB::raw('MAX(updated_at) as created_at'),DB::raw('MAX(pd_end) as pd_end'),DB::raw('MIN(pd_start) as pd_start'))
            ->where($query)
            ->where('check_no', $all['id'])
            ->orderBy('batches', 'desc')
            ->groupBy('batches','is_diff');
        $data = $builder->paginate($limit);

        return sendData(200, '', $data);
    }

    public function relieve(Request $request)
    {
        $all = $request->all();
        try {
            DB::beginTransaction();
            foreach ($all as $key => $val) {
                if (!isset($val['valnumber'])) {
                    $all[$key]['valnumber'] = $val['real_number'];
                }
            }
            foreach ($all as $key => $val) {
                if (!preg_match("/^\d*$/", $val['valnumber'])) {
                    return sendData(402, '只能输入0和正整数');
                }
                if ($val['valnumber'] < 0) {
                    return sendData(402, '只能输入0和正整数');
                }
                CheckTag::where('id', $val['id'])->update(['real_number' => $val['valnumber']]);
            }
            foreach ($all as $key =>$val){
                if($val['goods'] == null){
                    DB::table('check_tag')->where('batches',$val['batches'])->update(['is_diff'=>'有差异']);
                    break;
                }else{
                    $number = $val['goods']['number'] + $val['goods']['frozen_number'];
                    if($val['valnumber'] == $number){
                        DB::table('check_tag')->where('batches',$val['batches'])->update(['is_diff'=>'无差异']);
                    }else{
                        DB::table('check_tag')->where('batches',$val['batches'])->update(['is_diff'=>'有差异']);
                        break;
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }

    public function getGoodsByProductId(Request $request)
    {
        $params = $request->all();
        try {
            // 开启事务
            DB::beginTransaction();
            $check_no = DB::table('check_tag')->where('batches', $params['0']['code'])->pluck('check_no');
            foreach ($params as $key => $val) {
                foreach ($val['postGoods'] as $k => $item) {
                    if (preg_match("/^\d*$/", $item['real_number']) <= 0) {
                        return sendData(402, '请认真填写数量');
                    }
                    if ($item['real_number'] == 0) {
                        return sendData(402, '数量不能为零');
                    }
                    $data = [
                        'check_no' => $check_no['0'],
                        'product_id' => $item['id'],
                        'goods_id' => 0,
                        'stock_no' => $item['act_stock_no'],
                        'status' => 1,
                        'state' => $item['act_state_name'],
                        'available_time' => date('Ym', strtotime($item['act_available_time'])),
                        'real_number' => $item['real_number'],
                        'batches' => $params['0']['code'],
                        'is_ok' => 1,
                        'is_diff' => '有差异',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    DB::table('check_tag')->insert($data);
                }
            }
            DB::commit();
            return sendData(200, '添加成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function export(Request $request)
    {
        $OutStcNo = $request->all();
        $pieces = explode(",", $OutStcNo['id']);
        $salesOuts = CheckTag::whereIn('id', $pieces)->orderBy('batches')->with(['product', 'goods'])->get();
        $res = [[number_format($salesOuts['0']['batches'], 0, '', '')], ['库位号', '品名', '新产品代码', '产品代码', '有效期', '状态', '数量', '冻结数量', '实盘数量']];
        foreach ($salesOuts as $key => $value) {
            $res[] = [
                $value['stock_no'],
                $value['product']['PRODCHINM'],
                $value['product']['NewPRODUCTCD'],
                $value['product']['PRODUCTCD'],
                $value['available_time'],
                $value['state'],
                $value['goods']['number'],
                $value['goods']['frozen_number'],
                $value['real_number'],
            ];
        }
        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->rows($res);
            });
        })->export('xls');
    }

    public function getAllGoods(Request $request)
    {
        $params = $request->all();
        $where = [];
        try {
            DB::beginTransaction();
            foreach ($params as $key => $val) {
                if (empty($val['start']) || empty($val['end'])) {
                    return sendData(402, '请选择时间段');
                }
                if ($val['start'] > $val['end']) {
                    return sendData(402, '开始时间不能大于结束时间');
                }
                $starttime = date("Y-m-d H:i:s", strtotime($val['start']));
                $endtime = date("Y-m-d H:i:s", strtotime($val['end']) + 86399);
                $where[] = ['created_at', '>=', $starttime];
                $where[] = ['created_at', '<=', $endtime];
                $recode = DB::table('goods_record')
                    ->select('origin_stock_no', DB::raw('count(origin_stock_no) as type'))
                    ->where($where)
                    ->where('origin_stock_no', '<>', '复核区')
                    ->where('origin_stock_no', '<>', '移库区')
                    ->where('origin_stock_no', '<>', '加工区')
                    ->orderBy('type', 'desc')
                    ->orderBy('origin_stock_no', 'asc')
                    ->groupBy('origin_stock_no')
                    ->get();
            }
            if (count($recode) == 0) {
                return sendData(402, '这段时间库位没有异动');
            }
            foreach ($recode as $k => $item) {
                $data[] = [
                    'diff_code' => strval($params['0']['box_code']),
                    'stock_no' => $item->origin_stock_no,
                    'number' => $item->type,
                    'status' => 0,
                    'state' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
            DB::table('diff_check_tag')->insert($data);
            $res = [
                'code' => $params['0']['box_code'],
                'comment' => $params['0']['code'],
                'created_at' => date('Y-m-d H:i:s'),
                'rad' => $params['0']['rad'],
            ];
            DB::table('diff_check')->insert($res);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');

    }

    public function getAllProducts(Request $request)
    {
        $all = $request->get('codeInput');
        $newAll = $request->get('newcodeInput');

        if ($all) {
            $product = DB::table('product')->where('PRODUCTCD', $all)->get();
        } else {
            $product = DB::table('product')->where('NewPRODUCTCD', $newAll)->get();
        }
        return sendData(200, '', $product);
    }

    public function getDetailById(Request $request)
    {
        $id = $request->all();
        try {
            DB::beginTransaction();
            $where = [
                'status' => 1,
            ];
            DB::table('diff_check_tag')->whereIn('id', $id)->update($where);
            $res = DB::table('diff_check_tag')->whereIn('id', $id)->pluck('stock_no');
            $code = DB::table('diff_check_tag')->whereIn('id', $id)->pluck('diff_code');
            $rad = DB::table('diff_check')->where('code',$code[0])->pluck('rad');
            $goods = DB::table('goods')->whereIn('stock_no', $res)->get();
            $batches = DB::table('check_tag')->where('check_no', $code[0])->where('batches', '<>', '')->orderBy('batches', 'desc')->get()->toArray();

            if (empty($batches)) {
                $batches = $code[0] . '001';
            } else {
                $batches = number_format(strval($batches['0']->batches) + 1, 0, '', '');
            }
//            if(count($goods) == 0){
//                return sendData(402, '该库位现在没商品');
//            }
            $data = [];
            $pre = $prf = [];
            $pre_key = '';
            foreach ($goods as $k => $info) {
                $key = $info->product_id . '-' . $info->available_time;
                if ($k === 0) {
                    $pre[$key] = $info;
                    $pre_key = $key;
                    continue;
                }
                if (array_key_exists($key, $pre)) {
                    $pre[$key]->number += $info->number;
                } else {
                    $prf[] = $pre[$pre_key];
                    $pre = [];
                    $pre_key = $key;
                    $pre[$key] = $info;
                }
            }
//             最后一条特殊处理
            $prf[] = $pre[$pre_key];
            foreach ($prf as $key => $val) {
                $data[] = [
                    'check_no' => $code[0],
                    'product_id' => $val->product_id,
                    'goods_id' => $val->id,
                    'stock_no' => $val->stock_no,
                    'status' => 1,
                    'batches' => $batches,
                    'available_time' => $val->available_time,
                    'state' => $val->state_name,
                    'is_ok' => 0,
                    'is_diff' => '未录入',
                    'number' =>$val->number + $val->frozen_number,
                    'rad' => $rad[0],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            DB::table('check_tag')->insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }

    public function unfreezeIndex(Request $request)
    {
        $params = [];
        if ($request->get('code')) $params[] = ['code', 'like', '%' . $request->get('code')];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['created_at', '>=', $starttime];
            $params[] = ['created_at', '<=', $endtime];
        }
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $data = DB::table('diff_check')
            ->select('code', 'comment','rad',
                DB::raw('MAX(created_at) as created_at')
            )
            ->orderBy('created_at', 'desc')
            ->where($params)
            ->groupBy('code', 'comment','rad')
            ->paginate($limit);
        return sendData(200, '', $data);
    }

    public function getById(Request $request)
    {
        $frost = Frost::where('code', $request->get('id'))->with('goods', 'product')->get();
        return sendData(200, '', $frost);
    }

    public function unfreeze(Request $request)
    {
        $id = $request->get('id');
        $state = CheckTag::where('batches', $id)->orderBy('stock_no', 'asc')->with('goods', 'product')->get();
        return sendData(200, '', $state);
    }

    public function exportDiff(Request $request)
    {
        $OutStcNo = $request->all();
        $pieces = explode(",", $OutStcNo['id']);
        $salesOuts = CheckTag::whereIn('batches', $pieces)->orderBy('stock_no')->with(['product', 'goods'])->get();
        $res = [['盘点单号', $salesOuts['0']['check_no']], ['任务号', '库位号', '品名', '新产品代码', '产品代码', '有效期', '状态', '数量', '冻结数量', '实盘数量', '差异']];
        foreach ($salesOuts as $key => $value) {
            $res[] = [
                $value['batches'],
                $value['stock_no'],
                $value['product']['PRODCHINM'],
                $value['product']['NewPRODUCTCD'],
                $value['product']['PRODUCTCD'],
                $value['available_time'],
                $value['state'],
                $value['goods']['number'],
                $value['goods']['frozen_number'],
                $value['real_number'],
                $value['real_number'] - $value['goods']['number'] - $value['goods']['frozen_number'],
            ];
        }
        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->rows($res);
            });
        })->download('xls');
    }

    public function verify(Request $request)
    {
        $OutStcNo = $request->all();
        $salesOuts = CheckTag::whereIn('batches', $OutStcNo)->orderBy('batches', 'asc')->orderBy('stock_no', 'asc')->with(['product', 'goods'])->get();
        $res = [['盘点单号', $salesOuts['0']['check_no']], ['任务号', '库位号', '品名', '新产品代码', '产品代码', '有效期', '状态', '数量', '冻结数量', '实盘数量', '差异']];
        foreach ($salesOuts as $key => $value) {
            if (is_null($value['real_number'])) {
                return sendData(402, $value['batches'] . '的' . $value['stock_no'] . '的' . $value['product']['PRODUCTCD'] . '没有填写实盘数量');
            }
            $res[] = [
                $value['batches'],
                $value['stock_no'],
                $value['product']['PRODCHINM'],
                $value['product']['NewPRODUCTCD'],
                $value['product']['PRODUCTCD'],
                $value['available_time'],
                $value['state'],
                $value['goods']['number'],
                $value['goods']['frozen_number'],
                $value['real_number'],
                $value['real_number'] - $value['number'],
            ];
        }
        $name = date('YmdHis') . rand('1', '999');
        if (!is_dir(storage_path() . '/' . 'exports')) mkdir(storage_path() . '/' . 'exports');

        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->rows($res);
            });
        })->store('xls', public_path() . '/' . 'exports');
        return sendData(200, '', $name . '.xls');
    }

    public function getExport(Request $request)
    {
        $name = $request->get('name');
        return response()->download(public_path() . '/' . 'exports/' . $name)->deleteFileAfterSend(true);
    }

    public function shopping(Request $request)
    {
        $id = $request->all();
        try {
            DB::beginTransaction();
            $data = [
                'state' => 1,
            ];
            DB::table('diff_check_tag')->whereIn('id', $id)->update($data);
            DB::commit();
        } catch (\Exception $e) {
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }

    public function shoppingList(Request $request)
    {
        $all = $request->get('id');
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = [];
        if ($request->get('stock_no')) $params[] = ['stock_no', 'like', $request->get('stock_no') . '%'];
        $result = DB::table('diff_check_tag')->where('state', '1')->where('status','0')->where('diff_code', $all)->where($params);
        $data = $result->paginate($limit);
        return sendData(200, '请求成功', $data);
    }
//
    public function separate(Request $request)
    {
        $id = $request->get('id');
        $uid = DB::table('diff_check_tag')->where('diff_code',$id)->where('state',1)->pluck('id');
        $rad = DB::table('diff_check')->where('code',$id)->get();
        try {
            DB::beginTransaction();
            $res = DB::table('diff_check_tag')->whereIn('id', $uid)->pluck('stock_no');
            $code = DB::table('diff_check_tag')->whereIn('id', $uid)->pluck('diff_code');
            $goods = DB::table('goods')->whereIn('stock_no', $res)->get();
            $batches = DB::table('check_tag')->where('check_no', $code[0])->where('batches', '<>', '')->orderBy('batches', 'desc')->get()->toArray();
            if (empty($batches)) {
                $batches = $code[0] . '001';
            } else {
                $batches = number_format(strval($batches['0']->batches) + 1, 0, '', '');
            }
//            if(count($goods) == 0){
//                return sendData(402, '该库位现在没商品');
//            }
            $data = [];
            $pre = $prf = [];
            $pre_key = '';
            foreach ($goods as $k => $info) {
                $key = $info->product_id . '-' . $info->available_time;
                if ($k === 0) {
                    $pre[$key] = $info;
                    $pre_key = $key;
                    continue;
                }
                if (array_key_exists($key, $pre)) {
                    $pre[$key]->number += $info->number;
                } else {
                    $prf[] = $pre[$pre_key];
                    $pre = [];
                    $pre_key = $key;
                    $pre[$key] = $info;
                }
            }
//             最后一条特殊处理
            $prf[] = $pre[$pre_key];
            foreach ($prf as $key => $val) {
                $data[] = [
                    'check_no' => $code[0],
                    'product_id' => $val->product_id,
                    'goods_id' => $val->id,
                    'stock_no' => $val->stock_no,
                    'status' => 1,
                    'batches' => $batches,
                    'available_time' => $val->available_time,
                    'state' => $val->state_name,
                    'rad' =>$rad[0]->rad,
                    'is_diff' => '未录入',
                    'is_ok' => 0,
                    'number' =>$val->number + $val->frozen_number,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            DB::table('check_tag')->insert($data);
            DB::table('diff_check_tag')->whereIn('id',$uid)->update(['status'=>1,'state'=>2]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '请求成功');
    }
    public function del(Request $request)
    {
        $id = $request->all();
        try {
            DB::beginTransaction();
            DB::table('diff_check_tag')->whereIn('id', $id)->update(['state'=>0]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '请求成功');
    }

    public function batches(Request $request)
    {
        $all = $request->all();
        try {
            DB::beginTransaction();
            $batches = explode(",", $all['0']['old_code']);
            $salesOuts = CheckTag::whereIn('batches', $batches)->orderBy('batches', 'asc')->orderBy('stock_no', 'asc')->with(['product', 'goods'])->get();
            $list = [];
            foreach ($salesOuts as $key => $value) {
                if (is_null($value['real_number'])) {
                    return sendData(402, $value['batches'] . '的' . $value['stock_no'] . '的' . $value['product']['PRODUCTCD'] . '没有填写实盘数量');
                }
                if($value['real_number'] - $value['number']!= 0){
                    $list[] = $value['stock_no'];
                }
            }
            $insert = [
                'code'=>$all['0']['box_code'],
                'comment'=>$all['0']['code'],
                'rad'=>$all['0']['rad'],
                'created_at'=>date('Y-m-d H:i:s'),
            ];
            DB::table('diff_check')->insert($insert);
            $data = array_unique($list);
            $list = [];
            foreach ($data as $key=>$val){
                $list[] = [
                    'diff_code'=>$all['0']['box_code'],
                    'stock_no'=>$val,
                    'number'=>0,
                    'status'=>0,
                    'state'=>0,
                    'created_at'=>date('Y-m-d H:i:s')
                ];
            }
            DB::table('diff_check_tag')->insert($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }
}