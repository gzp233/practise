<?php

namespace App\Http\Controllers\Storage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use App\Models\Base\Product;
use Illuminate\Support\Facades\DB;
use Excel;
use App\Models\Storage\Frost;
use App\Models\Storage\GoodsModify;

class GoodsController extends Controller
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
            ->whereNotIn('g.stock_no', ['复核区', '移库区'])
            ->groupBy('product_id');

        $data = $data->paginate($limit);
        return sendData(200, '', $data);
    }

    public function goodsList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $params = [];
        if ($request->get('available_time')) $params[] = ['available_time', 'like', '%' . $request->get('available_time') . '%'];
        if ($request->get('stock_no')) $params[] = ['stock_no', '=', $request->get('stock_no')];
        if ($request->get('state_name')) $params[] = ['state_name', '=', $request->get('state_name')];
        if ($request->get('PRODCHINM')) $params[] = ['PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
        if ($request->get('NewPRODUCTCD')) $params[] = ['NewPRODUCTCD', '=', $request->get('NewPRODUCTCD')];
        if ($request->get('PRODUCTCD')) $params[] = ['PRODUCTCD', '=', $request->get('PRODUCTCD')];
        if ($request->get('BRANDNM')) $params[] = ['BRANDNM', '=', $request->get('BRANDNM')];
        if ($request->get('PRODFLGNM')) $params[] = ['PRODFLGNM', '=', $request->get('PRODFLGNM')];

        $builder = DB::table('goods as g')
            ->leftJoin('product as p', 'p.id', '=', 'g.product_id')
            ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
            ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
            ->select('g.*', 'p.PRODCHINM', 'p.NewPRODUCTCD', 'p.PRODUCTCD',  'b.BRANDNM', 'f.PRODFLGNM')
            ->where($params);
        if ($request->get('in_stock')) {
            $builder = $builder->whereNotIn('stock_no', ['复核区', '移库区', '加工区']);
        }
        if ($request->get('ids')) {
            $builder = $builder->whereNotIn('g.id', $request->get('ids'));
        }
        $res = $builder->orderBy($sort, 'asc')
            ->paginate($limit);
        return sendData(200, '请求成功', $res);
    }

    public function goodsList2(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = [];
        if ($request->get('available_time')) $params[] = ['available_time', 'like', '%' . $request->get('available_time') . '%'];
        if ($request->get('stock_no')) $params[] = ['stock_no', '=', $request->get('stock_no')];
        if ($request->get('state_name')) $params[] = ['state_name', '=', $request->get('state_name')];
        if ($request->get('PRODCHINM')) $params[] = ['PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
        if ($request->get('NewPRODUCTCD')) $params[] = ['NewPRODUCTCD', '=', $request->get('NewPRODUCTCD')];
        if ($request->get('PRODUCTCD')) $params[] = ['PRODUCTCD', '=', $request->get('PRODUCTCD')];
        if ($request->get('BRANDNM')) $params[] = ['BRANDNM', '=', $request->get('BRANDNM')];
        if ($request->get('PRODFLGNM')) $params[] = ['PRODFLGNM', '=', $request->get('PRODFLGNM')];

        $builder = DB::table('goods as g')
            ->leftJoin('product as p', 'p.id', '=', 'g.product_id')
            ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
            ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
            ->select(
                'g.product_id',
                'g.stock_no',
                'g.state_name',
                'g.available_time',
                DB::raw('sum(g.number) as number'),
                DB::raw('group_concat(g.id) as ids'),
                'p.PRODCHINM',
                'p.NewPRODUCTCD',
                'p.PRODUCTCD'
            )
            ->groupBy('g.product_id', 'g.stock_no', 'g.state_name', 'g.available_time', 'p.PRODCHINM', 'p.NewPRODUCTCD', 'p.PRODUCTCD')
            ->where($params);
        if ($request->get('in_stock')) {
            $builder = $builder->whereNotIn('stock_no', ['复核区', '移库区', '加工区']);
            $builder = $builder->whereNotIn('state_name', ['加工完成']);
        }
        if ($request->get('ids')) {
            $ids = [];
            foreach ($request->get('ids') as $item) {
                $ids = array_merge($ids, explode(',', $item));
            }
            $builder = $builder->whereNotIn('g.id', $ids);
        }
        $res = $builder->orderBy('g.stock_no', 'asc')
            ->paginate($limit);
        return sendData(200, '请求成功', $res);
    }

    public function getByNo(Request $request)
    {
        $all = $request->all();
        try {
            // 开启事务
            DB::beginTransaction();
            foreach ($all as $key => $item) {
                foreach ($item['postGoods'] as $ke => $val) {
                    if ($val['number'] < $val['valnumber']) {
                        return sendData(402, '请认真填写冻结数量');
                    }
                    $code = $item['code'];
                    $rand = $item['box_code'];
                    if (empty($val['number'])) {
                        continue;
                    }
                    if (preg_match("/^\d*$/", $val['valnumber']) <= 0) {
                        return sendData(402, '请认真填写冻结数量');
                    }
                    if ($val['valnumber'] == 0) {
                        return sendData(402, '冻结数量不能为零');
                    }
                    $data = [
                        'number' => $val['number'] - $val['valnumber'],
                        'frozen_number' => $val['frozen_number'] + $val['valnumber'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    DB::table('goods')->where('id', $val['id'])->update($data);
                    $row = [
                        'goods_id' => $val['id'],
                        'code' => strval($rand),
                        'state' => '已冻结',
                        'status' => '已冻结',
                        'stock_no' => $val['stock_no'],
                        'cause' => $code,
                        'product_id' => $val['product_id'],
                        'number' => $val['valnumber'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    DB::table('frost')->insert($row);
                }
            }
            DB::commit();
            return sendData(200, '冻结成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function relieve(Request $request)
    {
        $all = $request->all();
        foreach ($all as $key => $val) {
            $data = [
                'number' => $val['number'] + $val['frozen_number'],
                'frozen_number' => '0'
            ];
            DB::table('goods')->where('id', $val['id'])->update($data);
        }
        return sendData(200, '解冻成功');
    }

    public function getGoodsByProductId(Request $request)
    {
        $params = $request->all();
        if (empty($params['id'])) return sendData(402, 'ID错误');
        $query = [];
        $query[] = ['product_id', '=', $params['id']];

        if (!empty($params['state_name'])) $query[] = ['state_name', '=', $params['state_name']];
        if (!empty($params['CHARG'])) $query[] = ['CHARG', '=', $params['CHARG']];
        if (!empty($params['stock_no'])) $query[] = ['stock_no', 'like', ' % ' . $params['stock_no'] . ' % '];
        if (!empty($params['not_stock_no'])) $query[] = ['stock_no', ' <> ', $params['not_stock_no']];
        $goods = Goods::where($query)->with(['product'])->whereNotIn('stock_no', ['复核区', '移库区', '加工区'])->get();
        return sendData(200, '', $goods);
    }

    public function export(Request $request)
    {
        $OutStcNo = $request->all();
        $pieces = explode(",", $OutStcNo['id']);
        //        $salesOuts = Goods::whereIn('id', $pieces)->orderBy('stock_no')->with(['product'])->get();
        $salesOuts = DB::table('goods as g')
            ->leftJoin('product as p', 'p.id', '=', 'g.product_id')
            ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
            ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
            ->select('p.PRODCHINM', 'p.NewPRODUCTCD', 'p.PRODUCTCD', 'g.stock_no', 'g.state_name', 'g.available_time', 'g.number', 'g.frozen_number', 'b.BRANDNM', 'f.PRODFLGNM')
            ->whereIn('g.id', $pieces)
            ->get();

        if (count($salesOuts) == 0) {
            $salesOuts = DB::table('goods as g')
                ->leftJoin('product as p', 'p.id', '=', 'g.product_id')
                ->leftJoin('prod_flg as f', 'p.ProdFlg', '=', 'f.PRODFLG')
                ->leftJoin('brand as b', 'p.BRANDCD', '=', 'b.BRANDCD')
                ->select('p.PRODCHINM', 'p.NewPRODUCTCD', 'p.PRODUCTCD', 'g.stock_no', 'g.state_name', 'g.available_time', 'g.number', 'g.frozen_number', 'b.BRANDNM', 'f.PRODFLGNM')
                ->get();
        }
        $res = [['库位号', '品名', '新产品代码', '产品代码', '产品分类', '品牌分类', '状态', '有效期', '数量', '冻结数量']];
        foreach ($salesOuts as $key => $value) {
            $res[] = [
                $value->stock_no,
                $value->PRODCHINM,
                $value->NewPRODUCTCD,
                $value->PRODUCTCD,
                $value->PRODFLGNM,
                $value->BRANDNM,
                $value->state_name,
                $value->available_time,
                $value->number,
                $value->frozen_number,
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
        $query = [];
        $codeInput = [];
        if (!empty($params['product_id'])) $query[] = ['product_id', '=', $params['product_id']];
        if (!empty($params['codeInput'])) {
            $codeInput = DB::table('product')->where('PRODUCTCD', 'like', $params['codeInput'] . '%')->pluck('id');
        }
        if (empty($codeInput)) {
            $goods = Goods::where($query)
                ->whereNotIn('stock_no', ['复核区', '移库区', '加工区'])
                ->with(['product'])
                ->get();
        } else {
            $goods = Goods::where($query)
                ->whereNotIn('stock_no', ['复核区', '移库区', '加工区'])
                ->whereIn('product_id', $codeInput)
                ->with(['product'])
                ->get();
        }
        return sendData(200, '', $goods);
    }

    public function getAllProducts(Request $request)
    {
        $productIds = Goods::whereNotIn('state_name', ['加工完成'])->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->get();

        return sendData(200, '', $products);
    }

    public function getDetailById(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return sendData(402, 'ID不能为空');
        $goodsRecords = GoodsRecord::where(['related_id' => $id, 'type' => 'instorage_process'])
            ->with(['product'])
            ->get();
        return sendData(200, '', $goodsRecords);
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
        $data = DB::table('frost')
            ->select(
                'code',
                'status',
                'cause',
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('sum(number) as number'),
                DB::raw('MAX(created_at) as created_at')
            )
            ->orderBy('created_at', 'desc')
            ->where($params)
            ->groupBy('code', 'status', 'cause')
            ->paginate($limit);
        foreach ($data as $key => $item) {
            $res = DB::table('frost')->where('code', $item->code)->pluck('stock_no')->toArray();
            $result_01 = array_flip($res);
            $result = array_keys($result_01);
            $data[$key]->stock_no = count($result);
            $product = DB::table('frost')->where('code', $item->code)->pluck('product_id')->toArray();
            $resul = array_flip($product);
            $type = array_keys($resul);
            $data[$key]->type = count($type);
        }
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
        $state = DB::table('frost')->where('id', $id)->pluck('state');
        foreach ($state as $item) {
            if ($item == '已释放') {
                return sendData(402, '该产品已释放！');
            }
        }
        try {
            // 开启事务
            DB::beginTransaction();
            DB::table('frost')->where('id', $id)->update(['state' => '已释放']);
            $code = DB::table('frost')->where('id', $id)->first();
            $status = DB::table('frost')->where('code', $code->code)->get();
            $data = [];
            foreach ($status as $k => $v) {
                $data[] = $v->state;
            }
            $sto = array_flip($data);
            if (count($sto) == 1) {
                DB::table('frost')->where('code', $code->code)->update(['status' => '已释放']);
            }
            $goods = Frost::where('id', $id)->get();
            foreach ($goods as $key => $val) {
                $goodsNo = Goods::where('id', $val->goods_id)->first();
                $data = [
                    'number' => $goodsNo->number + $val->number,
                    'frozen_number' => $goodsNo->frozen_number - $val['number'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                DB::table('goods')->where('id', $val->goods_id)->update($data);
            }
            DB::commit();
            return sendData();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    // 修改库存
    public function modify(Request $request)
    {
        $id = $request->get('id');
        $num = $request->get('number');
        $comment = $request->get('comment');
        if (!$id || !$goods = Goods::find($id)) return sendData(402, 'ID错误');
        if ($num < 0 || $num == $goods->number) return sendData(402, '修改后的数量不能小于0或与原数量相等');
        if (!$comment) return sendData(402, '修改说明必填');

        try {
            DB::beginTransaction();
            $insert = [
                'product_id' => $goods->product_id,
                'user_id' => auth()->user()->id,
                'stock_no' => $goods->stock_no,
                'state_name' => $goods->state_name,
                'CHARG' => $goods->CHARG ? $goods->CHARG : '',
                'number' => $num - $goods->number,
                'origin_number' => $goods->number,
                'available_time' => $goods->available_time,
                'comment' => $comment
            ];
            GoodsModify::create($insert);
            if ($num == 0 && $goods->frozen_number == 0) {
                $goods->delete();
            } else {
                $goods->number = $num;
                $goods->save();
            }
            DB::commit();
            return sendData();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData();
    }

    // 修改记录展示
    public function modifyList(Request $request)
    {
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = [];
        if ($request->get('stock_no')) $params[] = ['stock_no', '=', $request->get('stock_no')];
        if ($request->get('state_name')) $params[] = ['state_name', '=', $request->get('state_name')];
        if ($request->get('available_time')) $params[] = ['available_time', '=', $request->get('available_time')];
        if ($request->get('user_id')) $params[] = ['user_id', '=', $request->get('user_id')];

        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['created_at', '>=', $starttime];
            $params[] = ['created_at', '<=', $endtime];
        }
        $list = GoodsModify::where($params)
            ->with(['user', 'product'])
            ->whereHas('product', function ($q) use ($request) {
                $p = [];
                if ($request->get('NewPRODUCTCD')) $p[] = ['NewPRODUCTCD', '=', $request->get('NewPRODUCTCD')];
                if ($request->get('PRODCHINM')) $p[] = ['PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%'];
                $q->where($p);
            })
            ->orderBy($sort, 'desc')
            ->paginate($limit);


        return sendData(200, '', $list);
    }
}
