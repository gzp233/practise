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

class JihuoController extends BarcodeController
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
        if ($res == 3) {
            return sendData(402, '请选择未处理的订单');
        }
        if ($res != 1) {
            return sendData(402, '请选择拣货中的订单');
        }
        $start = DB::table('ganher')->where('code', $id)->pluck('jh_start');
        if ($start[0] == null) {
            DB::table('ganher')->where('code', $id)->update(['jh_start' => date('Y-m-d H:i:s')]);
        }
        return sendData(200, '');
    }

    private function checkOrder(string $id): int
    {
        $where = [
            '未处理', '处理中'
        ];
        $vbeln = DB::table('ganher')->where('code', $id)->whereIn('status', $where)->pluck('vbeln')->toArray();
        if (count($vbeln) == 0) {
            return 3;
        }
        $result_01 = array_flip($vbeln);
        $result_02 = array_flip($result_01);
        $pieces = array_merge($result_02);
        $goods_id = DB::table('goods_tag')->whereIn('odd', $pieces)->pluck('goods_id');
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
    public function getJihuoStockList(Request $request)
    {
        $id = $request->get('id');
        $vbeln = DB::table('ganher')->where('code', $id)->pluck('vbeln')->toArray();
        if (count($vbeln) == 0) return sendData(402, '集货单错误，请刷新页面');
        $result_01 = array_flip($vbeln);
        $result_02 = array_flip($result_01);
        $pieces = array_merge($result_02);
        $records = GoodsRecord::where('stock_no', '复核区')->whereIn('odd', $pieces)->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])->orderBy('origin_stock_no', 'asc')->get();
        if (count($records) == 0) return sendData(402, '集货单错误，请刷新页面');
        $list = [];
        foreach ($records as $record) {
            if (!in_array($record->origin_stock_no, $list)) {
                $list[$record->origin_stock_no] = ['stock_no' => $record->origin_stock_no, 'status' => 0];
            }
        }
        //从redis获取已经扫描的库位,变更状态
        $stockData =  json_decode(Redis::get('jihuo::' . $id));

        if ($stockData) {
            foreach ($stockData as $stock_no => $data) {
                $list[$stock_no]['status'] = $data->status;
            }
        }
        return sendData(200, '', $list);
    }
    public function getJihuoStock(Request $request)
    {
        $id = $request->get('id');
        $stock_no = $request->get('stock_no');
        if (!$id || !$stock_no) return sendData(402, '信息错误');

        $prf = [];
        // 从redis获取数据
        $stockData =  json_decode(Redis::get('jihuo::' . $id));
        if ($stockData) {
            foreach ($stockData as $key => $data) {
                if ($stock_no == $key) $prf = $data->data;
            }
        }
        if (!$prf) {
            $vbeln = DB::table('ganher')->where('code', $id)->pluck('vbeln')->toArray();
            $result_01 = array_flip($vbeln);
            $result_02 = array_flip($result_01);
            $pieces = array_merge($result_02);
            $records = GoodsRecord::where(['stock_no' => '复核区', 'origin_stock_no' => $stock_no])
                ->whereIn('odd', $pieces)
                ->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])
                ->with('product', 'unit')
                ->orderBy('product_id', 'asc')
                ->get();
            $pre = $prf = [];
            $pre_key = '';
            foreach ($records as $k => $info) {
                $product = Product::where('id',$info->product_id)->first();
                $key = $product->PRODUCTCD . '-' . $info->available_time;
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
            //最后一条特殊处理
            $prf[] = $pre[$pre_key];
            $surplus = 0;
            $box = 0;
            foreach ($prf as $record) {
                foreach ($record->unit as $key => $val) {
                    if ($val->unit_name == '箱') {
                        if ($val->unit_name == '箱') {
                            $surplus = $record->number % $val->number;
                            if ($surplus == 0) {
                                $box = $record->number / $val->number;
                                $surplus = 0;
                            } else {
                                $box = ($record->number - $surplus) / $val->number;
                            }
                        }
                    } else {
                        continue;
                    }
                }
                $record->scanNumber = 0;
                $record->surplus = $surplus;
                $record->box = $box;
            }
        }

        return sendData(200, '', $prf);
    }
    public function doJihuoStock(Request $request)
    {
        $records = $request->all();
        // 查看是否扫描完成
        $start = $count = 0;
        foreach ($records as $item) {
            if ($item['scanNumber'] > 0) $start = 1;
            if ($item['scanNumber'] == $item['number']) $count++;
        }

        $data = [
            'status' => $count == count($records) ? 2 : ($start == 0 ? 0 : 1),
            'data' => $records
        ];
        $code = DB::table('ganher')->where('vbeln', $records[0]['odd'])->get();
        if (count($code) == 0) {
            return sendData(402, '集货单号不存在');
        }
        $stockData = json_decode(Redis::get('jihuo::' . $code[0]->code));
        $stock_no = $records[0]['origin_stock_no'];
        if (!$stockData) {
            $stockData = new \stdClass();
        }
        $stockData->$stock_no = $data;

        Redis::set('jihuo::' . $code[0]->code, json_encode($stockData));
        DB::table('ganher')->where('code', $code[0]->code)->update(['status' => '处理中']);
        return sendData();
    }

    public function doJihuo(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return sendData(402, '单号不能为空');
        }
        if (!$stockData = json_decode(Redis::get('jihuo::' . $id))) {
            return sendData(402, '该单未开始拣货！');
        }
        $vbeln = DB::table('ganher')->where('code', $id)->pluck('vbeln')->toArray();
        $result_01 = array_flip($vbeln);
        $result_02 = array_flip($result_01);
        $pieces = array_merge($result_02);
        $records = GoodsRecord::where(['stock_no' => '复核区'])->whereIn('odd', $pieces)->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])->get();
        if (count($records) == 0) return sendData(402, '拣货单错误，请刷新页面');
        $relatedIds = [];
        foreach ($records as $item) {
            $relatedIds[] = $item->related_id;
            $stock_no = $item->origin_stock_no;
            if (!isset($stockData->$stock_no) || $stockData->$stock_no->status != 2) {
                return sendData(402, '未拣货完成，请先完成拣货');
            }
        }
        DB::table('ganher')->where('code', $id)->update(['status' => '已完成', 'jh_end' => date('Y-m-d H:i:s')]);
        Redis::set('jihuo::' . $id, null);

        return sendData();
    }
}
