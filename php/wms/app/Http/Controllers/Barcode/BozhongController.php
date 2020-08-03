<?php
/**
 * Created by Valley.
 * User: Valley
 * Date: 2019/05/08
 * Time: 15:48
 */
namespace App\Http\Controllers\Barcode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\Redis;
use App\Models\Base\Product;

class BozhongController extends BarcodeController
{
    /**
     * Create a new GoodsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function getByNo(Request $request)
    {
        $id = $request->get('id');
        $res = $this->checkOrder($id);
        if ($res != 1) {
            return sendData(402, '请选择已完成的订单');
        }
        $start = DB::table('ganher')->where('code',$id)->pluck('bz_start');
        if ($start[0] == null) {
            DB::table('ganher')->where('code', $id)->update(['bz_start' => date('Y-m-d H:i:s')]);
        }
        return sendData(200, '');
    }

    private function checkOrder(string $id): int
    {
        $vbeln = DB::table('ganher')->where('code',$id)->where('status','已完成')->pluck('vbeln')->toArray();
        if(count($vbeln) == 0){
            return 3;
        }
        $result_01 = array_flip($vbeln);
        $result_02 = array_flip($result_01);
        $pieces = array_merge($result_02);
        $goods_id = DB::table('goods_tag')->whereIn('odd',$pieces)->pluck('goods_id');
        $goodsId = DB::table('goods')->whereIn('id', $goods_id)->get();
        if (count($goodsId) == 0) {
            return 0;
        }
        $moveCode = DB::table('move_out_dirt')->whereIn('MoveNo', $pieces)->pluck('id');
        $adjCode = DB::table('adj_out_dirt')->whereIn('AdjustNo', $pieces)->pluck('id');
        $ordCode = DB::table('ord_out_dirt')->whereIn('OrderNo', $pieces)->pluck('id');
        $move = DB::table('move_out_dirt_tag')->whereIn('related_id', $moveCode)->pluck('status');
        $adj = DB::table('adj_out_dirt_tag')->whereIn('related_id', $adjCode)->pluck('status');
        $ord = DB::table('ord_out_dirt_tag')->whereIn('related_id', $ordCode)->pluck('status');

        if (count($move) != 0) {
            if ($move[0] == '拣货中') {
                return 1;
            }

            if ($move[0] == '复核中') {
                return 2;
            }
        }
        if (count($adj) != 0) {
            if ($adj[0] == '拣货中') {
                return 1;
            }

            if ($adj[0] == '复核中') {
                return 2;
            }
        }
        if (count($ord) != 0) {
            if ($ord[0] == '拣货中') {
                return 1;
            }

            if ($ord[0] == '复核中') {
                return 2;
            }
        }

        return 0;
    }

    public function getBozhongStockList(Request $request)
    {
        $id = $request->get('id');
        $vbeln = DB::table('ganher')->where('code',$id)->where('status','已完成')->pluck('vbeln')->toArray();
        if (count($vbeln) == 0) return sendData(402, '集货播种错误，请刷新页面');
        $result_01 = array_flip($vbeln);
        $result_02 = array_flip($result_01);
        $pieces = array_merge($result_02);
        $records = GoodsRecord::where('stock_no','复核区')->whereIn('odd',$pieces)->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])->with('product')->orderBy('origin_stock_no','asc')->get();
        if (count($records) == 0) return sendData(402, '集货播种错误，请刷新页面');
        $list = [];
       

        foreach ($records as $record) {
            if (!in_array($record->product->PRODUCTCD, $list)) {
                $list[$record->product->PRODUCTCD] = ['PRODUCTCD' => $record->product->PRODUCTCD, 'status' => 0];
            }
        }

        //从redis获取已经扫描的库位,变更状态
        $stockData =  json_decode(Redis::get('bozhong::' . $id));
        if ($stockData) {
            foreach ($stockData as $stock_no => $data) {
                $list[$stock_no]['status'] = $data->status;
            }
        }
        return sendData(200, '', $list);
    }

    public function getProduct(Request $request)
    {
        $all = $request->get('id');
        $len = strlen($all);
        $product = '';
        if($len == 23){
            $product = substr($all,9,11);
            $PRODUCTCD = Product::where('NewPRODUCTCD',$product)->first();
            if(!$PRODUCTCD){
                return sendData(402, '91码错误');
            }
            $product = $PRODUCTCD->PRODUCTCD;
        }
        if($len == 13 || $len == 14){
            $PRODUCTCD = Product::where('barCode',$all)->first();
            if(!$PRODUCTCD){
               return sendData(402, '支码错误');
            }
            $product = $PRODUCTCD->PRODUCTCD;
        }
        return sendData(200, '', $product);
    }

    public function getbozhongStock(Request $request)
    {
        $id = $request->get('id');
        $stock_no = $request->get('stock_no');
        if (!$id || !$stock_no) return sendData(402, '信息错误');
        $records = [];
        // 从redis获取数据
        $stockData =  json_decode(Redis::get('bozhong::' . $id));
        if ($stockData) {
            foreach ($stockData as $key => $data) {
                foreach ($data->data as $k =>$v){
                    if ($stock_no == $v->product->PRODUCTCD) $records = $data->data;
                }
            }
        }
        $pre = $prf = [];
        if (!$records) {
            $vbeln = DB::table('ganher')->where('code',$id)->pluck('vbeln')->toArray();
            $result_01 = array_flip($vbeln);
            $result_02 = array_flip($result_01);
            $pieces = array_merge($result_02);
            $product = Product::where('PRODUCTCD',$stock_no)->pluck('id')->toArray();
            if(count($product) == 0){
                return sendData(402, '没有找到该商品');
            }
            $records = GoodsRecord::where(['stock_no' => '复核区',])
                ->whereIn('odd',$pieces)
                ->whereIn( 'product_id', $product)
                ->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])
                ->with('product')
                ->get();
            $pre_key = '';
            foreach ($records as $k => $info) {
                $key = $info->odd;
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
            foreach ($prf as $record) {
                $record->scanNumber = '';
                $record->WriteNumber = '';
            }
        } else {
            $prf = $records;
        }
        return sendData(200, '', $prf);
    }

    public function doBozhongStock(Request $request)
    {
        $records = $request->all();
        // 查看是否扫描完成
        $start = $count = 0;
        foreach ($records as $item) {
            if ($item['WriteNumber'] > 0) $start = 1;
            if ($item['WriteNumber'] == $item['number'] && $item['scanNumber'] == $item['odd']) $count++;
        }
        $data = [
            'status' => $count == count($records) ? 2 : ($start == 0 ? 0 : 1),
            'data' => $records
        ];
        $code = DB::table('ganher')->where('vbeln',$records[0]['odd'])->get();
        if(count($code) == 0){
            return sendData(402, '集货单号不存在');
        }
        $stockData = json_decode(Redis::get('bozhong::' . $code[0]->code));
        $stock_no = $records[0]['product']['PRODUCTCD'];
        if (!$stockData) {
            $stockData = new \stdClass();
        }
        $stockData->$stock_no = $data;
        Redis::set('bozhong::' . $code[0]->code, json_encode($stockData));
        return sendData();
    }

    public function doBozhong(Request $request)
    {
        $id = $request->get('id');
        try {
            DB::beginTransaction();
        if (!$id) {
            return sendData(402, '单号不能为空');
        }
        if (!$stockData = json_decode(Redis::get('bozhong::' . $id))) {
            return sendData(402, '该单未开始播种！');
        }
        $vbeln = DB::table('ganher')->where('code',$id)->pluck('vbeln')->toArray();
        $result_01 = array_flip($vbeln);
        $result_02 = array_flip($result_01);
        $pieces = array_merge($result_02);
        $records = GoodsRecord::where(['stock_no' => '复核区'])->whereIn('odd',$pieces)->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])->with('product')->get();
        if (count($records) == 0) return sendData(402, '播种错误，请刷新页面');
        $relatedIds = [];
        foreach ($records as $item) {
            $relatedIds[] = $item->related_id;
            $stock_no = $item->product['PRODUCTCD'];
            if (!isset($stockData->$stock_no) || $stockData->$stock_no->status != 2) {
                return sendData(402, '未拣货完成，请先完成拣货');
            }
            DB::table($item->type . '_tag')->where('related_id', $item->related_id)->update(['status' => '复核中']);
        }
            DB::table('ganher')->where('code',$id)->update(['status'=>'播种完成','bz_end'=>date('Y-m-d H:i:s')]);
            Redis::set('bozhong::' . $id, null);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData();
    }
}