<?php

namespace App\Http\Controllers\Instorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Instorage\InstorageProcess;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use App\Models\Base\Product;

class InstorageProcessController extends InBaseController
{
    protected $rules = [];
    /**
     * Create a new InstorageProcessController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    /**
     * 分页获取退货入库指令
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = InstorageProcess::whereHas('product', function ($q) use ($request) {
            if ($request->get('PRODCHINM')) $q->where('PRODCHINM', 'like', '%' . $request->get('PRODCHINM') . '%');
            if ($request->get('NewPRODUCTCD')) $q->where('NewPRODUCTCD',  'like','%'. $request->get('NewPRODUCTCD'));
        })
            ->with('product')
            ->orderBy($sort, 'desc')
            ->paginate($limit);

        return sendData(200, '', $data);
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

    public function getAllProducts(Request $request)
    {
        $productIds = Goods::where('state_name','C302')->whereNotIn('stock_no', ['加工区', '复核区', '移库区'])->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->get();

        return sendData(200, '', $products);
    }

    public function getAllGoods(Request $request)
    {
        $params = $request->all();
        $query = [];
        $query[] = ['state_name', '=', 'C302'];
        if (!empty($params['product_id'])) $query[] = ['product_id', '=', $params['product_id']];
        $goods = Goods::where($query)->whereNotIn('stock_no', ['加工区', '复核区', '移库区'])
            ->with(['product'])
            ->get();

        return sendData(200, '', $goods);
    }

    public function getGoodsByIds(Request $request)
    {
        $params = $request->all();
        if (empty($params)) return sendData(402, 'ID为空');
        $ids = [];
        foreach ($params as $item) {
            $tmp = explode(',', $item);
            foreach ($tmp as $v) {
                $ids[] = $v;
            }
        }
        $goods = Goods::whereIn('id', $ids)->with(['product','tag'])->get();
        return sendData(200, '', $goods);
    }

    public function move(Request $request)
    {
        $goodsList = $request->all();
        if (empty($goodsList)) return sendData(402, '选择的商品不能为空！');

        $process = [];
        foreach ($goodsList as $item) {
            if (isset($process[$item['product_id']])) {
                $process[$item['product_id']]['number'] += $item['number'];
            } else {
                $process[$item['product_id']] = ['number' => $item['number']];
            }
        }
        try {
            // 开启事务
            DB::beginTransaction();
            
             //插入InstorageProcess表
            foreach ($process as $product_id => $value) {
                $id = InstorageProcess::insertGetId([
                    'product_id' => $product_id,
                    'number' => $value['number'],
                    'type' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $process[$product_id]['insertId'] = $id;
            }

            foreach ($goodsList as $goods) {
                $item = Goods::find($goods['id']);
                if ($item->product_id != $goods['product_id'] || $item->number != $goods['number']) {
                    throw new \Exception('库存发生变化，请刷新页面');
                }
                $orign_stock_no = $item->stock_no;
                $item->state_name = 'C302';
                $item->stock_no = '加工区';
                $item->save();
                $goods_id = DB::table('goods_tag')->where('goods_id',$goods['id'])->first();
                if(!$goods_id){
                    $goods_id->odd = '';
                }
                // 存入副表
                $goodsRecord = [
                    'product_id' => $item->product_id,
                    'stock_no' => $item->stock_no,
                    'state_name' => $item->state_name,
                    'origin_stock_no' => $orign_stock_no,
                    'CHARG' => $item->CHARG,
                    'available_time' => $item->available_time,
                    'number' => $item->number,
                    'type' => 'instorage_process',
                    'odd' => $goods_id->odd,
                    'related_id' => $process[$item->product_id]['insertId'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                GoodsRecord::create($goodsRecord);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }

        return sendData(200, '创建成功!');
    }

    /* 上架 */
    public function stockIn(Request $request)
    {
        $params = $request->all();
        if (empty($params)) {
            return sendData(402, '未选择入库商品！');
        }

        $process = [];
        foreach ($params as $item) {
            if (empty($item['act_stock_no'])) return sendData(402, '库位必选！');
            if (empty($item['act_state_name'])) return sendData(402, '状态必选！');
            if (empty($item['act_number'])) return sendData(402, '数量必填！');
            if (isset($process[$item['product_id']])) {
                $process[$item['product_id']]['number'] += $item['act_number'];
            } else {
                $process[$item['product_id']] = ['number' => $item['act_number']];
            }
        }

        try {
            // 开启事务
            DB::beginTransaction();
            $exists = [];
            
            //插入InstorageProcess表
            foreach ($process as $product_id => $value) {
                $id = InstorageProcess::insertGetId([
                    'product_id' => $product_id,
                    'number' => $value['number'],
                    'type' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $process[$product_id]['insertId'] = $id;
            }

            // 循环库位
            foreach ($params as $item) {
                // 插入goods表和副表
                $goods = Goods::find($item['id']);
                if (!in_array($goods->id, $exists)) {
                    if ($goods->number != $item['number']) throw new \Exception('库存数量发生变化，无法入库！');
                    $exists[] = $goods->id;
                }

                if ($goods->number == $item['act_number']) {
                    $goods->delete();
                } else {
                    $goods->number -= $item['act_number'];
                    $goods->save();
                }
                $total = $item['act_number'];
                $act = $item['act_number'];
                $list = [
                    'product_id'=>$item['product']['id'],
                    'stock_no'=>$item['stock_no'],
                    'state_name'=>$item['state_name'],
                    'CHARG'=>$item['CHARG'],
                    'available_time'=>$item['available_time'],
                    'odd'=>$item['tag']['odd']
                ];
                $list = GoodsRecord::where($list)->first();
                if($list){
                    $code = [
                        'product_id'=>$item['product']['id'],
                        'stock_no'=>$list['origin_stock_no'],
                        'state_name'=>$item['state_name'],
                        'CHARG'=>$item['CHARG'],
                        'available_time'=>$item['available_time'],
                        'odd'=>$item['tag']['odd']
                    ];
                }else {
                    continue;
                }
                $code = GoodsRecord::where($code)->first();
                if($code){
                    $code->number -= $act;
                    if($code->number == 0){
                        $code->delete();
                        $act = 0;
                    }else {
                        $code->save();
                        $act = 0;
                    }
                }
                $arr = [
                    'product_id'=>$item['product']['id'],
                    'stock_no'=>$item['act_stock_no'],
                    'state_name'=>$item['act_state_name'],
                    'CHARG'=>$item['CHARG'],
                    'available_time'=>$item['available_time'],
                    'odd'=>$item['tag']['odd']
                ];
                $arr = GoodsRecord::where($arr)->first();
                if($arr){
                    $arr->number += $total;
                    $arr->save();
                    $total = 0;
                }else{ 
                    $arr = [
                        'product_id' => $item['product_id'],
                        'stock_no' => $item['act_stock_no'],
                        'state_name' => $item['act_state_name'],
                        'CHARG' => $item['CHARG'],
                        'number' => $total,
                        'available_time' => $item['available_time'],
                        'type' => $code['type'],
                        'odd' => $code['odd'],
                        'related_id' => $code['related_id'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    GoodsRecord::insert($arr);
                    $total = 0;
                }

                $goodsData = [
                    'product_id' => $item['product']['id'],
                    'stock_no' => $item['act_stock_no'],
                    'state_name' => $item['act_state_name'],
                    'CHARG' => $item['CHARG'],
                    'available_time' => $item['available_time'],
                ];
                $flag = DB::table('goods_tag')->where('odd',$item['tag']['odd'])->pluck('goods_id')->toArray();
                $goodsS = Goods::where($goodsData)->whereIn('id',$flag)->first();
                if ($goodsS) {
                    $goodsS->number += $item['act_number'];
                    $goodsS->save();
                } else {
                    $goodsData['number'] = $item['act_number'];
                    $goodsData['created_at'] = $goodsData['updated_at'] = date('Y-m-d H:i:s');
                    $res = Goods::insertGetId($goodsData);
                    DB::table('goods_tag')->insert(['goods_id'=>$res,'odd'=>$item['tag']['odd']]);
                }
                $goodsData['number'] = $item['act_number'];
                $goodsData['created_at'] = $goodsData['updated_at'] = date('Y-m-d H:i:s');
                $goodsData['origin_stock_no'] = '加工区';
                $goodsData['odd'] = $item['tag']['odd'];
                $goodsData['type'] = 'instorage_process';
                $goodsData['related_id'] = $process[$item['product']['id']]['insertId'];
                GoodsRecord::insert($goodsData);
                
               
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData(200, 'ok');
    }
}