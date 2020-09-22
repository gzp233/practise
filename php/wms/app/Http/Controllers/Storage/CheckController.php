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
use Milon\Barcode\DNS1D;
use PHPExcel_Worksheet_Drawing;

class CheckController extends Controller
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
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $params = $q = [];
        if ($request->get('available_time')) $params[] = ['g.available_time', 'like', '%' . $request->get('available_time') . '%'];
        if ($request->get('stock_no')) $params[] = ['c.stock_no', 'like', $request->get('stock_no') . '%'];
        if ($request->get('state_name')) $params[] = ['g.state_name', '=', $request->get('state_name')];
        if ($request->get('PRODCHINM')) $params[] = ['p.PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
        if ($request->get('NewPRODUCTCD')) $params[] = ['p.NewPRODUCTCD', '=', $request->get('NewPRODUCTCD')];
        if ($request->get('PRODUCTCD')) $params[] = ['p.PRODUCTCD', '=', $request->get('PRODUCTCD')];
        if ($request->get('PRODFLGNM')) $params[] = ['f.PRODFLGNM', '=', $request->get('PRODFLGNM')];
        if ($request->get('BRANDNM')) $params[] = ['b.BRANDNM', '=', $request->get('BRANDNM')];

        $result = DB::table('check_tag as c')
            ->leftJoin('goods as g', 'c.goods_id', '=', 'g.id')
            ->leftJoin('product as p', 'c.product_id', '=', 'p.id')
            ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
            ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
            ->select('c.id', 'p.PRODCHINM', 'p.PRODUCTCD', 'p.NewPRODUCTCD', 'b.BRANDNM', 'f.PRODFLGNM', 'c.stock_no', 'g.state_name', 'g.available_time', 'g.frozen_number', 'g.number')
            ->where('c.check_no', $request->get('id'))
            ->where('c.status', '0')
            ->where($params)
            ->orderBy('c.stock_no', 'asc');
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
            ->select('batches', 'is_diff', DB::raw('MAX(fp_time) as created_at'), DB::raw('MAX(pd_end) as pd_end'), DB::raw('MIN(pd_start) as pd_start'))
            ->where($query)
            ->where('check_no', $all['id'])
            ->orderBy('batches', 'desc')
            ->groupBy('batches', 'is_diff');
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

                DB::table('check_tag')->where('id', $val['id'])->update(['real_number' => $val['valnumber']]);
            }
            foreach ($all as $key => $val) {
                if ($val['goods'] == null) {
                    DB::table('check_tag')->where('batches', $val['batches'])->update(['is_diff' => '有差异']);
                    break;
                } else {
                    $number = $val['goods']['number'] + $val['goods']['frozen_number'];
                    if ($val['valnumber'] == $number) {
                        DB::table('check_tag')->where('batches', $val['batches'])->update(['is_diff' => '无差异']);
                    } else {
                        DB::table('check_tag')->where('batches', $val['batches'])->update(['is_diff' => '有差异']);
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
        $salesOuts = DB::table('check_tag as c')
            ->leftJoin('goods as g', 'c.goods_id', '=', 'g.id')
            ->leftJoin('product as p', 'c.product_id', '=', 'p.id')
            ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
            ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
            ->select('c.batches', 'c.id', 'p.PRODCHINM', 'c.number', 'c.state', 'p.PRODUCTCD', 'p.NewPRODUCTCD', 'b.BRANDNM', 'f.PRODFLGNM', 'c.stock_no', 'c.available_time', 'c.real_number')
            ->whereIn('c.id', $pieces)
            ->orderBy('c.batches')
            ->get();
        if (count($salesOuts) == 0) {
            return '没有数据';
        }
        DNS1D::getBarcodePNGPath($salesOuts[0]->batches, "C39");
//        $salesOuts = CheckTag::whereIn('id', $pieces)->orderBy('batches')->with(['product', 'goods'])->get();
        $res = [[number_format($salesOuts['0']->batches, 0, '', '')], ['库位号', '品名', '新产品代码', '产品代码', '产品分类', '品牌分类', '有效期', '状态', '盘点数量', '实盘数量']];
        foreach ($salesOuts as $key => $value) {
            $res[] = [
                $value->stock_no,
                $value->PRODCHINM,
                $value->NewPRODUCTCD,
                $value->PRODUCTCD,
                $value->PRODFLGNM,
                $value->BRANDNM,
                $value->available_time,
                $value->state,
                $value->number,
                $value->real_number,
            ];
        }
        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path() . '/' . $res[0][0] . '.png');
                $objDrawing->setHeight(400);
                $objDrawing->setWidth(300);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setOffsetX(100);//写入图片在指定格中的X坐标值
                $objDrawing->setOffsetY(-25);//写入图片在指定格中的Y坐标值
                $objDrawing->setRotation(1);//设置旋转角度
                $objDrawing->getShadow()->setVisible(true);//
                $objDrawing->getShadow()->setDirection(50);//
                $objDrawing->setWorksheet($sheet);
                $sheet->rows($res);
            });
        })->export('xls');
    }

    public function getAllGoods(Request $request)
    {
        $params = $request->all();
        try {
            DB::beginTransaction();
            $goods = DB::table('goods as g')
                ->rightJoin('stock as s', 's.stock_no', '=', 'g.stock_no')
                ->select('g.id', 's.stock_no', 'g.product_id', 'g.available_time', 'g.state_name', 'g.number', 'g.frozen_number')
                ->leftJoin('product as p', 'g.product_id', '=', 'p.id')->get();
            $data = [];
            $pre = $prf = [];
            $pre_key = '';
            foreach ($goods as $k => $info) {
                $key = $info->product_id . '-' . $info->available_time . '-' . $info->stock_no . '-' . $info->state_name;
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
            foreach ($prf as $good => $key) {
                $data = [
                    'check_no' => strval($params['0']['box_code']),
                    'product_id' => $key->product_id,
                    'goods_id' => $key->id,
                    'stock_no' => $key->stock_no,
                    'status' => 0,
                    'available_time' => $key->available_time,
                    'state' => $key->state_name,
                    'is_ok' => 0,
                    'rad' => $params[0]['rad'],
                    'is_diff' => '未录入',
                    'number' => $key->number + $key->frozen_number,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                DB::table('check_tag')->insert($data);
            }
            $where = [
                'code' => $params['0']['box_code'],
                'comment' => $params['0']['code'],
                'rad' => $params[0]['rad'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
            DB::table('check')->insert($where);
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
            $check = CheckTag::whereIn('id', $id)->pluck('check_no')->toArray();
            $batches = DB::table('check_tag')->where('check_no', $check[0])->where('batches', '<>', '')->orderBy('batches', 'desc')->get()->toArray();
            if (empty($batches)) {
                $where = [
                    'status' => 1,
                    'batches' => $check[0] . '001',
                    'fp_time' =>date('Y-m-d H:i:s'),
                ];
            } else {
                $where = [
                    'status' => 1,
                    'batches' => number_format(strval($batches['0']->batches) + 1, 0, '', ''),
                    'fp_time' =>date('Y-m-d H:i:s'),
                ];
            }
            CheckTag::whereIn('id', $id)->update($where);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }
    public function getDetailByIds(Request $request)
    {
        if ($request->get('available_time')) $params[] = ['g.available_time', 'like', '%' . $request->get('available_time') . '%'];
        if ($request->get('stock_no')) $params[] = ['c.stock_no', 'like', $request->get('stock_no') . '%'];
        if ($request->get('state_name')) $params[] = ['g.state_name', '=', $request->get('state_name')];
        if ($request->get('PRODCHINM')) $params[] = ['p.PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
        if ($request->get('NewPRODUCTCD')) $params[] = ['p.NewPRODUCTCD', '=', $request->get('NewPRODUCTCD')];
        if ($request->get('PRODUCTCD')) $params[] = ['p.PRODUCTCD', '=', $request->get('PRODUCTCD')];
        if ($request->get('PRODFLGNM')) $params[] = ['f.PRODFLGNM', '=', $request->get('PRODFLGNM')];
        if ($request->get('BRANDNM')) $params[] = ['b.BRANDNM', '=', $request->get('BRANDNM')];

        $result = DB::table('check_tag as c')
            ->leftJoin('goods as g', 'c.goods_id', '=', 'g.id')
            ->leftJoin('product as p', 'c.product_id', '=', 'p.id')
            ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
            ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
            ->select('c.id', 'p.PRODCHINM', 'p.PRODUCTCD', 'p.NewPRODUCTCD', 'b.BRANDNM', 'f.PRODFLGNM', 'c.stock_no', 'g.state_name', 'g.available_time', 'g.frozen_number', 'g.number')
            ->where('c.check_no', $request->get('id'))
            ->where('c.status', '0')
            ->where($params)
            ->orderBy('c.stock_no', 'asc')
            ->pluck('id');
            try {
                DB::beginTransaction();
                $check = CheckTag::whereIn('id', $result)->pluck('check_no')->toArray();
                $batches = DB::table('check_tag')->where('check_no', $check[0])->where('batches', '<>', '')->orderBy('batches', 'desc')->get()->toArray();
                if (empty($batches)) {
                    $where = [
                        'status' => 1,
                        'batches' => $check[0] . '001',
                    ];
                } else {
                    $where = [
                        'status' => 1,
                        'batches' => number_format(strval($batches['0']->batches) + 1, 0, '', ''),
                    ];
                }
                CheckTag::whereIn('id', $result)->update($where);
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
        if ($request->get('status') && in_array($request->get('status'), ['已冻结', '已释放'])) {
            $params[] = ['status', '=', $request->get('status')];
        }
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $data = DB::table('check')
            ->select('code', 'comment', 'rad',
                DB::raw('MAX(created_at) as created_at')
            )
            ->orderBy('created_at', 'desc')
            ->where($params)
            ->groupBy('code', 'comment', 'rad')
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
        $state = CheckTag::where('batches', $id)->with('goods', 'product')->get();
        return sendData(200, '', $state);
    }

    public function shopping(Request $request)
    {
        $id = $request->all();
        try {
            DB::beginTransaction();
            $check = CheckTag::whereIn('id', $id)->get()->toArray();
            foreach ($check as $item => $key) {
                $data = [
                    'check_tag_id' => $key['id'],
                    'stock_no' => $key['stock_no'],
                    'check_tag_no' => $key['check_no'],
                    'goods_id' => $key['goods_id'],
                    'product_id' => $key['product_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                DB::table('shopping')->insert($data);
            }
            CheckTag::whereIn('id', $id)->update(['status' => 1]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }
    public function shoppingIndex (Request $request){
        if ($request->get('available_time')) $params[] = ['g.available_time', 'like', '%' . $request->get('available_time') . '%'];
        if ($request->get('stock_no')) $params[] = ['c.stock_no', 'like', $request->get('stock_no') . '%'];
        if ($request->get('state_name')) $params[] = ['g.state_name', '=', $request->get('state_name')];
        if ($request->get('PRODCHINM')) $params[] = ['p.PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
        if ($request->get('NewPRODUCTCD')) $params[] = ['p.NewPRODUCTCD', '=', $request->get('NewPRODUCTCD')];
        if ($request->get('PRODUCTCD')) $params[] = ['p.PRODUCTCD', '=', $request->get('PRODUCTCD')];
        if ($request->get('PRODFLGNM')) $params[] = ['f.PRODFLGNM', '=', $request->get('PRODFLGNM')];
        if ($request->get('BRANDNM')) $params[] = ['b.BRANDNM', '=', $request->get('BRANDNM')];

        $result = DB::table('check_tag as c')
            ->leftJoin('goods as g', 'c.goods_id', '=', 'g.id')
            ->leftJoin('product as p', 'c.product_id', '=', 'p.id')
            ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
            ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
            ->select('c.id', 'p.PRODCHINM', 'p.PRODUCTCD', 'p.NewPRODUCTCD', 'b.BRANDNM', 'f.PRODFLGNM', 'c.stock_no', 'g.state_name', 'g.available_time', 'g.frozen_number', 'g.number')
            ->where('c.check_no', $request->get('id'))
            ->where('c.status', '0')
            ->where($params)
            ->orderBy('c.stock_no', 'asc')
            ->pluck('id');
            $check = CheckTag::whereIn('id', $result)->get()->toArray();
            try {
                DB::beginTransaction();
            foreach ($check as $item => $key) {
                $data = [
                    'check_tag_id' => $key['id'],
                    'stock_no' => $key['stock_no'],
                    'check_tag_no' => $key['check_no'],
                    'goods_id' => $key['goods_id'],
                    'product_id' => $key['product_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                DB::table('shopping')->insert($data);
            }
            CheckTag::whereIn('id', $result)->update(['status' => 1]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }
    public function shoppingList(Request $request)
    {
        $id = $request->get('id');
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $params = $q = [];
        if ($request->get('available_time')) $params[] = ['g.available_time', 'like', '%' . $request->get('available_time') . '%'];
        if ($request->get('state_name')) $params[] = ['g.state_name', '=', $request->get('state_name')];
        if ($request->get('PRODCHINM')) $params[] = ['p.PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
        if ($request->get('stock_no')) $params[] = ['g.stock_no', 'like', $request->get('stock_no') . '%'];
        if ($request->get('NewPRODUCTCD')) $params[] = ['p.NewPRODUCTCD', '=', $request->get('NewPRODUCTCD')];
        if ($request->get('PRODUCTCD')) $params[] = ['p.PRODUCTCD', '=', $request->get('PRODUCTCD')];

        $result = DB::table('shopping as s')
            ->leftJoin('goods as g', 's.goods_id', '=', 'g.id')
            ->leftJoin('product as p', 's.product_id', '=', 'p.id')
            ->select('s.id', 's.stock_no', 'p.PRODCHINM', 'p.PRODUCTCD', 'p.NewPRODUCTCD', 'g.state_name', 'g.available_time', 'g.frozen_number', 'g.number')
            ->where($params)
            ->where(['check_tag_no' => $id])
            ->orderBy('s.stock_no', 'asc');
        $data = $result->paginate($limit);
        return sendData(200, '请求成功', $data);
    }

    public function separate(Request $request)
    {
        $all = $request->all();
        try {
            DB::beginTransaction();
            $id = DB::table('shopping')->whereIn('check_tag_no', $all)->pluck('check_tag_id');
            $check = CheckTag::whereIn('id', $id)->pluck('check_no')->toArray();
            $batches = DB::table('check_tag')->where('check_no', $check[0])->where('batches', '<>', '')->orderBy('batches', 'desc')->get()->toArray();
            if (empty($batches)) {
                $where = [
                    'status' => 1,
                    'batches' => $check[0] . '001',
                    'fp_time' =>date('Y-m-d H:i:s'),
                ];
            } else {
                $where = [
                    'status' => 1,
                    'batches' => number_format(strval($batches['0']->batches) + 1, 0, '', ''),
                    'fp_time' =>date('Y-m-d H:i:s'),
                ];
            }
            CheckTag::whereIn('id', $id)->update($where);
            DB::table('shopping')->whereIn('check_tag_no', $all)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }

    public function del(Request $request)
    {
        $id = $request->all();
        try {
            DB::beginTransaction();
            $check_tag_id = DB::table('shopping')->whereIn('id', $id)->pluck('check_tag_id');
            CheckTag::whereIn('id', $check_tag_id)->update(['status' => 0]);
            DB::table('shopping')->whereIn('id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '请求成功');
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
        })->export('xls');
    }

    public function verify(Request $request)
    {
        $OutStcNo = $request->all();
        $salesOuts = CheckTag::whereIn('batches', $OutStcNo)->orderBy('batches', 'asc')->orderBy('stock_no', 'asc')->with(['product', 'goods', 'user'])->get();
        $res = [['盘点单号', $salesOuts['0']['check_no']], ['任务号', '库位号', '品名', '新产品代码', '产品代码', '有效期', '状态', '盘点数量', '实盘数量', '差异', '操作员', '操作开始时间', '操作结束时间']];
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
                $value['number'],
                $value['real_number'],
                $value['real_number'] - $value['number'],
                $value['user']['username'],
                $value['pd_start'],
                $value['pd_end']
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
                if ($value['real_number'] - $value['number'] != 0) {
                    $list[] = $value['stock_no'];
                }
            }
            $insert = [
                'code' => $all['0']['box_code'],
                'comment' => $all['0']['code'],
                'rad' => $all['0']['rad'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
            DB::table('check')->insert($insert);
            $goods = DB::table('goods')->whereIn('stock_no', $list)->get();
            $data = [];
            $pre = $prf = [];
            $pre_key = '';
            foreach ($goods as $k => $info) {
                $key = $info->product_id . '-' . $info->available_time . '-' . $info->stock_no . '-' . $info->state_name;
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
                    'check_no' => $all['0']['box_code'],
                    'product_id' => $val->product_id,
                    'goods_id' => $val->id,
                    'stock_no' => $val->stock_no,
                    'status' => 0,
                    'available_time' => $val->available_time,
                    'state' => $val->state_name,
                    'number' => $val->number+$val->frozen_number,
                    'is_ok' => 0,
                    'rad' => $all['0']['rad'],
                    'is_diff' => '未录入',
                    'created_at' => date('Y-m-d H:i:s'),
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
}