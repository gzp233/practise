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
use App\Models\Storage\CheckTag;
use Illuminate\Support\Facades\Log;

class PandianController extends BarcodeController
{
    /**
     * Create a new GoodsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->user = auth()->user();
    }

    public function checkPandianOrder(Request $request)
    {
        $id = $request->get('id');
        $res = DB::table('check_tag')->where('batches', $id)->get();
        if (count($res) == 0) {
            return sendData(402, '该单号不存在');
        }
        if ($res[0]->finish == 1) {
            return sendData(402, '该单号已盘点完');
        }
        return sendData(200, '');
    }

    public function getPandianStockList(Request $request)
    {
        $id = $request->get('id');
        $batches = DB::table('check_tag as c')
            ->leftJoin('user as u', 'u.id', '=', 'c.user_id')
            ->where('batches', $id)
            ->get();
        if (count($batches) == 0) return sendData(402, '盘点单错误，请刷新页面');
        $list = [];
        foreach ($batches as $record) {
            if (!in_array($record->stock_no, $list)) {
                $list[$record->stock_no] = ['stock_no' => $record->stock_no, 'status' => $record->is_ok, 'username' => $record->username];
            }
        }
        return sendData(200, '', $list);
    }
    public function getPandianStock(Request $request)
    {
        $id = $request->get('id');
        $stock_no = $request->get('stock_no');
        if (!$id || !$stock_no) return sendData(402, '信息错误');
        $user = CheckTag::where('batches', $id)->where('stock_no', $stock_no)->get();
        if(!$user){
            return sendData(402, '信息错误');
        }
        foreach($user as $val){
            if ($val->is_ok == 1) {
                return sendData(402, '该库位已盘点');
            }
            if ($val['user_id'] == null) {
                $data = [
                    'is_ok' => 2,
                    'user_id' => $this->user->id,
                ];
                CheckTag::where('id', $val['id'])->update($data);
            } else {
                if ($val['user_id'] != $this->user->id) {
                    return sendData(200, '用户错误');
                }
            }
            if ($val['pd_start'] == null) {
                CheckTag::where('id', $val['id'])->update(['pd_start' =>date('Y-m-d H:i:s')]);
            }
        }
        
        
        $records = CheckTag::where('batches', $id)->where('stock_no', $stock_no)->orderBy('stock_no', 'asc')->with('goods', 'product')->get();
        foreach ($records as $record) {
            $record->scanNumber = 0;
        }
        return sendData(200, '', $records);
    }

    public function barCode(Request $request)
    {
        $code = $request->get('code');
        if (strlen($code) == 12 || strlen($code) == 13 || strlen($code) == 14) {
            $res = DB::table('product')->where('barCode', $code)->pluck('NewPRODUCTCD')->toArray();
            if (count($res) == 0) {
                return sendData(402, '请填写支码');
            }
        } elseif (strlen($code) == 11) {
            $res = DB::table('product')->where('NewPRODUCTCD', $code)->pluck('validity')->toArray();
            if (count($res) == 0) {
                return sendData(402, '请填写效期');
            }
        } else {
            return sendData(402, '信息错误');
        }
        return sendData(200, '', $res);
    }
    public function doPandianStock(Request $request)
    {
        $records = $request->all();
        $id = $request->get('id');
        $stock_no = $request->get('stock_no');
        try {
            DB::beginTransaction();
            if ($id) {
                $data = [
                    'is_ok' => 1,
                    'real_number' => '0',
                    'submit' => '1',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'pd_end' => date('Y-m-d H:i:s'),
                ];
                CheckTag::where('batches', $id)->where('stock_no', $stock_no)->update($data);
            } else {
                foreach ($records as $record => $value) {
                    $submit = CheckTag::where('id', $value['id'])->first();                
                    if($submit->submit == 0){
                    if ($value['goods_id'] == 0) {
                        $product = DB::table('product')->where('NewPRODUCTCD', $value['product']['NewPRODUCTCD'])->get();
                        $pd_start = CheckTag::where('batches', $value['batches'])->where('stock_no', $value['stock_no'])->pluck('pd_start');
                        $data = [
                            'check_no' => substr($value['batches'], 0, 8),
                            'product_id' => $product[0]->id,
                            'goods_id' => 0,
                            'stock_no' => $value['stock_no'],
                            'status' => 1,
                            'batches' => $value['batches'],
                            'real_number' => $value['scanNumber'],
                            'state' => '',
                            'user_id' => $this->user->id,
                            'available_time' => $value['available_time'],
                            'is_ok' => 1,
                            'number' => 0,
                            'submit' => '1',
                            'created_at' => date('Y-m-d H:i:s'),
                            'pd_start' => $pd_start[0],
                            'pd_end' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        DB::table('check_tag')->insert($data);
                    } else {
                        $tag = CheckTag::where('id', $value['id'])->first();
                        $tag->is_ok = 1;
                        $tag->submit = 1;
                        $tag->real_number = $value['scanNumber'];
                        $tag->updated_at = date('Y-m-d H:i:s');
                        $tag->pd_end = date('Y-m-d H:i:s');
                        $tag->save();
                        }
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


    public function doPandian(Request $request)
    {
        $id = $request->get('id');
        $isOk = DB::table('check_tag')->where('batches', $id)->pluck('is_ok')->toArray();
        $result_01 = array_flip($isOk);
        $result_02 = array_flip($result_01);
        $result    = array_merge($result_02);
        if (count($result) != 1) {
            return sendData(402, '该单未盘点完');
        } elseif ($result[0] != 1) {
            return sendData(402, '该单未盘点完');
        } else {
            DB::table('check_tag')->where('batches', $id)->update(['finish' => 1]);
        }
        $res = DB::table('check_tag')->where('batches', $id)->get();
        foreach ($res as $list => $val) {
            if ($val->real_number - $val->number != 0) {
                DB::table('check_tag')->where('batches', $id)->update(['is_diff' => '有差异']);
                break;
            } else {
                DB::table('check_tag')->where('batches', $id)->update(['is_diff' => '无差异']);
            }
        }

        return sendData(200, '成功');
    }
}
