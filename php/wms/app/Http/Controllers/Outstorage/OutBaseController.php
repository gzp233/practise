<?php

namespace App\Http\Controllers\Outstorage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use App\Models\Outstorage\AntiCode;
use App\Jobs\SendAntiCode;
use App\Models\Outstorage\OrderOutEnsure;
use App\Models\Outstorage\Adjust;
use App\Models\Outstorage\MoveOut;
use App\Models\Outstorage\SalesOut;
use Illuminate\Support\Facades\Cache;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Redis;


class OutBaseController extends Controller
{
    private $quantity = [
        'ord_out_dirt' => 'AdmQnty',
        'move_out_dirt' => 'MovAdmQnty',
        'adj_out_dirt' => 'AdjustQnty',
    ];

    protected function outStorage(array $params, $tagModel, $type, $odd)
    {
        $orders = $params['orders'];
        if (empty($orders)) return '参数错误';
        try {
            // 开启事务
            DB::beginTransaction();
            if ($orders[0]['tag'] && $orders[0]['tag']['status'] == '待发运') {
                // 检查防串货
                $this->checkFangchuanhuo($orders[0], $type);
            }

            $collection = collect($orders);
            $collection = $collection->map(function ($item, $key) {
                return collect($item['id'])->except('id');
            });
            $plucked = $collection->pluck('0')->all();
            $res = $tagModel::whereIn('related_id', $plucked)->get();
            if (count($res) != 0) {
                foreach ($orders as $order) {
                    $where = [];
                    $where[] = ['related_id', '=', $order['id']];
                    $where[] = ['status', '=', '拣货中'];
                    $count = $tagModel::where($where)->count();
                    if ($count == 1) {
                        $ganher = DB::table('ganher')->where('OutStcNo',$order['OutStcNo'])->get();
                        if(count($ganher) != 0){
                            if($ganher[0]->status == '未集货'){
                                $tagModel::where('related_id', $order['id'])->update(['status' => '复核中']);
                            }else{
                                return '该订单已集货';
                            }
                        }
                        $tagModel::where('related_id', $order['id'])->update(['status' => '复核中']);
                    } else {
                        $query = [];
                        $query[] = ['related_id', '=', $order['id']];
                        $query[] = ['status', '=', '复核中'];
                        $count = $tagModel::where($query)->count();
                        if ($count != 0) {
                            $tagModel::where('related_id', $order['id'])->update(['status' => '待发运']);
                        } else {
                            $query = [];
                            $query[] = ['related_id', '=', $order['id']];
                            $query[] = ['status', '=', '待发运'];
                            $count = $tagModel::where($query)->count();
                            if ($count != 0) {
                                $upData = ['status' => '发货完成'];
                                $tagModel::where('related_id', $order['id'])->update($upData);
                                foreach ($order['stocks'] as $item) {
                                    $goods_id = DB::table('goods_tag')->where('odd',$item['odd'])->pluck('goods_id')->toArray();
//                                    $condition = [
//                                        'CHARG' => $item['CHARG'],
//                                        'available_time' => $item['available_time'],
//                                        'frozen_number' => 0,
//                                        'stock_no' => $item['stock_no'],
//                                        'product_id' => $item['product_id'],
//                                        'number' => $item['number'],
//                                        'odd' => $item['odd']
//                                    ];
                                    Goods::whereIn('id',$goods_id)->delete();
                                    DB::table('goods_tag')->where('odd',$item['odd'])->delete();
                                    $record = [
                                        'related_id' => $order['id'],
                                        'type' => $type
                                    ];
                                    GoodsRecord::where($record)->update(['status' => '1']);
                                }
                            }
                        }
                    }
                }
            } else {
                foreach ($orders as $order) {
                    if ($order[$this->quantity[$type]] > $order['total']) throw new \Exception('库存不足，无法出库！');
                    // 插入副表
                    if (empty($order['stocks'])) throw new \Exception('库存不足，无法出库！');

                    $query = [
                        ['related_id', '=', $order['id']],
                        ['product_id', '=', $order['product']['id']],
                    ];
                    $count = $tagModel::where($query)->count();
                    if ($count != 0) continue;
                    $tagModel::insert([
                        'related_id' => $order['id'],
                        'product_id' => $order['product']['id'],
                        'number' => $order[$this->quantity[$type]],
                        'status' => '拣货中',
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    if($type == 'ord_out_dirt'){
                        DB::table('ganher')->insert([
                            'vbeln' =>$order['OrderNo'],
                            'OutStcNo' =>$order['OutStcNo'],
                            'FineFlg' =>$order['FineFlg'],
                            'number' =>$order['AdmQnty'],
                            'NewProductCd' =>$order['NewProductCd'],
                            'status' =>'未集货',
                            'ShopSignNM' => $order['customer']['ShopSignNM'],
                            'code' => '',
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    if($type == 'move_out_dirt'){
                        DB::table('ganher')->insert([
                            'vbeln' =>$order['MoveNo'],
                            'OutStcNo' =>$order['OutStcNo'],
                            'status' =>'未集货',
                            'FineFlg' =>$order['FineFlg'],
                            'number' =>$order['MovAdmQnty'],
                            'NewProductCd' =>$order['NewProductCD'],
                            'code' => '',
                            'ShopSignNM' => $order['deliver']['DeliverAddNM'],
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    if($type == 'adj_out_dirt'){
                        DB::table('ganher')->insert([
                            'vbeln' =>$order['AdjustNo'],
                            'OutStcNo' =>$order['OutStcNo'],
                            'FineFlg' =>$order['FineFlg'],
                            'status' =>'未集货',
                            'number' =>$order['AdjustQnty'],
                            'NewProductCd' =>$order['NewProductCd'],
                            'code' => '',
                            'ShopSignNM' => $order['customer']['ShopSignNM'],
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }

                    //更新库存表
                    foreach ($order['stocks'] as $item) {
                        if (empty($item['actNumber'])) continue;
                        $list = DB::table('goods')->where('id',$item['id'])->get()->toArray();
                        if(count($list) == 0){
                            return '商品库存状态发生变化,请刷新页面!';
                        }
                        $res = Goods::findOrFail($item['id']);
                        // 检查库存商品和状态
                        if (!$res || $res->number != $item['number'] || $res->number < $item['actNumber']) {
                            return '商品库存状态发生变化,请刷新页面!';
                        } elseif ($res->number == $item['actNumber'] && $res->frozen_number == 0) {
                            $res->delete();
                        } else {
                            $res->number = $res->number - $item['actNumber'];
                            $res->save();
                        }
                        $goodsData = [
                            'product_id' => $item['product_id'],
                            'stock_no' => '复核区',
                            'state_name' => $item['state_name'],
                            'CHARG' => $item['CHARG'],
                            'number' => $item['actNumber'],
                            'available_time' => $item['available_time'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        $goods_id = Goods::insertGetId($goodsData);
                        $data = [
                            'goods_id'=>$goods_id,
                            'odd'=>$odd
                        ];
                        DB::table('goods_tag')->insert($data);
                        $goodsData['type'] = $type;
                        $goodsData['related_id'] = $order['id'];
                        $goodsData['odd'] = $odd;
                        $goodsData['origin_stock_no'] = $item['stock_no'];
                        $goodsData['origin_stock_no'] = $item['stock_no'];
                        GoodsRecord::insert($goodsData);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        SendAntiCode::dispatch();
        return null;
    }

    private function checkFangchuanhuo($params, $type)
    {
        $map = [
            'move_out_dirt' => [
                'model' => new MoveOut(), 
                'column' => 'MoveNo', 
            ],
            'ord_out_dirt' => [
                'model' => new SalesOut(), 
                'column' => 'OrderNo', 
            ],
            'adj_out_dirt' => [
                'model' => new Adjust(), 
                'column' => 'AdjustNo', 
            ],
        ];
        $orderNo = $params[$map[$type]['column']];
        $goods = $map[$type]['model']::where($map[$type]['column'], $orderNo)
            ->whereHas('product', function($q) {
                $q->where('is_need_code', '是');
            })
            ->with(['tag', 'product'])
            ->get();
        if (count($goods) > 0) {
            $antis = AntiCode::where(['SHIPMENTID' => $orderNo, 'status' => 1])->whereNull('error')->get();
            $tmp = [];
            foreach ($antis as $anti) {
                if(isset($tmp[$anti->PRODUCTCODE])) {
                    $tmp[$anti->PRODUCTCODE] += $anti->NUM;
                } else {
                    $tmp[$anti->PRODUCTCODE] = $anti->NUM;
                }
            }
            foreach ($goods as $item) {
                if (!isset($tmp[$item->product->PRODUCTCD])) throw new \Exception('防串货未扫码完成');
                if ($tmp[$item->product->PRODUCTCD] != $item->tag->number) throw new \Exception('防串货未扫码完成');
            }
        }

        return true;
    }

    public function generate($outStcNo,$time)
    {
        try {
            //开启事务
            DB::beginTransaction();
            $orders = SalesOut::where('OutStcNo', $outStcNo)->get()->toArray();
            // $tag = DB::table('ord_out_dirt_tag')->where('related_id',$orders[0]['id'])->get()->toArray();
            // if($tag[0]->status =='待发运'){
            //     return sendData(402, '该订单已回传');
            // }
            $this->genTxt('ord_out_dirt', $orders,$time);
            foreach ($orders as $key => $item) {
                $arr = [
                    'VBELN' => $item['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $item['LineNo'],
                    'MATNR' => $item['NewProductCd'],
                    'AdmQnty' => $item['AdmQnty'],
                    'OrdTyep' => $item['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $item['PlaceCD'],
                    'LGORT' => $item['FineFlg'],
                    'PRODUCTCD' => $item['ProductCd'],
                ];
                $arr['created_at'] = date('Y-m-d H:i:s');
                OrderOutEnsure::Insert($arr);
                $data = ['zt'=>'已回传'];
                DB::table('ord_out_dirt')->where('id',$item['id'])->update($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData(200, 'ok!');
    }

    private function genTxt($type, $data,$time)
    {
        // $time = date('YmdHis',strtotime('+1second'));
        // if (Cache::get('generate_file_lock') == 1) {
        //     $time = date('YmdHis',strtotime('+1second'));
        // } else {
        //     Cache::put('generate_file_lock', 1, 0.1);
        //     $time = date('YmdHis');
        // }
        while(Redis::get($time)){
            $time++;
        }
        Redis::setex($time,300,$time);
        $time = date('YmdHis',$time);
        $path = storage_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'OUT' . DIRECTORY_SEPARATOR;
        switch ($type) {
            case 'move_out_dirt':
                $fileName = 'OrdOutEnsure7858' . $time . $data['0']['OutStcNo'] . '.txt';
                $location = $path . $fileName;
                foreach ($data as $value) {
                    $item = $this->getOtherData($type, $value);
                    if (!file_exists($location)) {
                        file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
                    }
                    file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
                }
                $okName = 'LO0067858' . $time . '.ok';
                file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
                break;
            case 'ord_out_dirt':
                $fileName = 'OrdOutEnsure7858' . $time . $data['0']['OutStcNo'] . '.txt';
                $location = $path . $fileName;
                foreach ($data as $value) {
                    $item = $this->getOtherData($type, $value);
                    if (!file_exists($location)) {
                        file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
                    }
                    file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
                }
                $okName = 'LO0067858' . $time . '.ok';
                file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
                break;
            case 'adj_out_dirt':
                $fileName = 'OrdOutEnsure7858' . $time . $data['0']['OutStcNo'] . '.txt';
                $location = $path . $fileName;
                foreach ($data as $value) {
                    $item = $this->getOtherData($type, $value);
                    if (!file_exists($location)) {
                        file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
                    }
                    file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
                }
                $okName = 'LO0067858' . $time . '.ok';
                file_put_contents($path . $okName, $fileName . "\x08" . filesize($location), LOCK_EX);
                break;
        }
        Redis::set('outFlag',null);
        return null;
    }

    private function getOtherData($type, $order)
    {
        $arr = [];
        switch ($type) {
            case 'ord_out_dirt':
                $arr = [
                    'VBELN' => $order['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $order['LineNo'],
                    'MATNR' => $order['NewProductCd'],
                    'AdmQnty' => $order['AdmQnty'],
                    'OrdTyep' => $order['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $order['PlaceCD'],
                    'LGORT' => $order['FineFlg'],
                    'PRODUCTCD' => $order['ProductCd'],
                ];
                break;
            case 'adj_out_dirt':
                $arr = [
                    'VBELN' => $order['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $order['LineNo'],
                    'MATNR' => $order['NewProductCd'],
                    'AdmQnty' => $order['AdjustQnty'],
                    'OrdTyep' => $order['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $order['PlaceCD'],
                    'LGORT' => $order['FineFlg'],
                    'PRODUCTCD' => $order['ProductCd'],
                ];
                break;
            case 'move_out_dirt':
                $arr = [
                    'VBELN' => $order['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $order['LineNo'],
                    'MATNR' => $order['NewProductCD'],
                    'AdmQnty' => $order['MovAdmQnty'],
                    'OrdTyep' => $order['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $order['MovFrmCD'],
                    'LGORT' => $order['FineFlg'],
                    'PRODUCTCD' => $order['ProductCD'],
                ];
                break;
        }
        return $arr;
    }

    public function moveByNo($outStcNo,$time)
    {
        try {
            // 开启事务
            DB::beginTransaction();
            $orders = MoveOut::where('OutStcNo', $outStcNo)->get()->toArray();
            // $tag = DB::table('move_out_dirt_tag')->where('related_id',$orders[0]['id'])->get()->toArray();
            // if($tag[0]->status =='已回传'){
            //     return sendData(402, '该订单已回传');
            // }
            // 生成TXT和插入
            $this->genTxt('move_out_dirt', $orders,$time);
            foreach ($orders as $key => $item) {
                $arr = [
                    'VBELN' => $item['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $item['LineNo'],
                    'MATNR' => $item['NewProductCD'],
                    'AdmQnty' => $item['MovAdmQnty'],
                    'OrdTyep' => $item['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $item['MovFrmCD'],
                    'LGORT' => $item['FineFlg'],
                    'PRODUCTCD' => $item['ProductCD'],
                ];
                $arr['created_at'] = date('Y-m-d H:i:s');
                OrderOutEnsure::Insert($arr);
                $data = ['zt'=>'已回传'];
                DB::table('move_out_dirt')->where('id',$item['id'])->update($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }


        return sendData(200, 'ok!');
    }

    public function adjustNo($outStcNo,$time)
    {
        try {
            //开启事务
            DB::beginTransaction();
            $orders = Adjust::where('OutStcNo', $outStcNo)->get()->toArray();
            // 生成TXT和插入
            $this->genTxt('adj_out_dirt', $orders,$time);
            foreach ($orders as $key => $item) {
                $arr = [
                    'VBELN' => $item['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $item['LineNo'],
                    'MATNR' => $item['NewProductCd'],
                    'AdmQnty' => $item['AdjustQnty'],
                    'OrdTyep' => $item['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $item['PlaceCD'],
                    'LGORT' => $item['FineFlg'],
                    'PRODUCTCD' => $item['ProductCd'],
                ];
                $arr['created_at'] = date('Y-m-d H:i:s');
                OrderOutEnsure::Insert($arr);
                $data = ['zt'=>'已回传'];
                DB::table('adj_out_dirt')->where('id',$item['id'])->update($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData(200, 'ok!');
    }
    
}