<?php

namespace App\Http\Controllers\Instorage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use App\Models\Instorage\ProdEnsureOther;
use App\Models\Instorage\ProdEnsureImport;
use App\Models\Outstorage\OrderOutEnsure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Basic\Location;
use App\Models\Basic\Attributes;
class InBaseController extends Controller
{
    protected function inStorage(array $params, $model, $type)
    {
        if (empty($params)) {
            return '未选择入库商品！';
        }
        $name = date('Y-m-d');
        Log::useFiles(storage_path('logs/jiagong/log' . $name . '.log'));

        foreach ($params as $item) {
            if (empty($item['act_stock_no'])) return '库位必选！';
            if (empty($item['act_state_name'])) return '状态必选！';
            if (empty($item['act_number'])) return '数量必填！';
            if (empty($item['act_available_time'])) return '有效期必填！';
            if (empty($item['product'])) return '该商品未检索到数据，无法入库！';
            //检查入库单状态是否发生变化
            $tag = $model::where([
                ['related_id', '=', $item['id']],
                ['product_id', '=', $item['product']['id']]
            ])->first();
            if ($tag && $tag->number != $item['tag']['number']) return '入库单状态发生变化,请刷新页面！';
        }

        try {
            // 开启事务
            DB::beginTransaction();
            // 循环库位
            foreach ($params as $order) {
                //插入tag表
                $tag = $model::where([
                    ['related_id', '=', $order['id']],
                    ['product_id', '=', $order['product']['id']]
                ])->first();
                if ($tag) {
                    $tag->number = $tag->number + $order['act_number'];
                    if ($type == 'prod_imp') {
                        if ($tag->number > $order['ImportQnty']) {
                            return '入库数量大于订单数量';
                        }
                    }
                    if ($type == 'asn') {
                        if ($tag->number > $order['InQnty']) {
                            return '入库数量大于订单数量';
                        }
                    }
                    if ($type == 'adj_out_dirt') {
                        if ($tag->number > $order['AdjustQnty']) {
                            return '入库数量大于订单数量';
                        }
                    }
                    if ($type == 'move_in_dirt') {
                        if ($tag->number > $order['MovAdmQnty']) {
                            return '入库数量大于订单数量';
                        }
                    }
                    if ($type == 'ret_in_dirt') {
                        if ($tag->number > $order['AdmQnty']) {
                            return '入库数量大于订单数量';
                        }
                    }
                    $tag->save();
                } else {
                    if ($order['act_number'] > $order['todoNumber']) {
                        return '入库数量大于订单数量';
                    }
                    $tagData = [
                        'related_id' => $order['id'],
                        'product_id' => $order['product']['id'],
                        'number' => $order['act_number'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    $model::insert($tagData);
                }
                if ($type == 'prod_imp') {
                    $sum = DB::table($type)->where('InvoiceNo', $order['InvoiceNo'])->sum('ImportQnty');
                    $id =  DB::table($type)->where('InvoiceNo', $order['InvoiceNo'])->pluck('id');
                    $number = $model::whereIn('related_id', $id)->sum('number');
                    if ($sum == $number) {
                        DB::table($type)->where('InvoiceNo', $order['InvoiceNo'])->update(['state' => '已入库']);
                    } else {
                        DB::table($type)->where('InvoiceNo', $order['InvoiceNo'])->update(['state' => '入库中']);
                    }
                }
                if ($type == 'asn') {
                    $sum = DB::table($type)->where('CustomerOrderNo', $order['CustomerOrderNo'])->sum('InQnty');
                    $id =  DB::table($type)->where('CustomerOrderNo', $order['CustomerOrderNo'])->pluck('id');
                    $number = $model::whereIn('related_id', $id)->sum('number');
                    if ($sum == $number) {
                        DB::table($type)->where('CustomerOrderNo', $order['CustomerOrderNo'])->update(['state' => '已入库']);
                    } else {
                        DB::table($type)->where('CustomerOrderNo', $order['CustomerOrderNo'])->update(['state' => '入库中']);
                    }
                }
                if ($type == 'adj_out_dirt') {
                    $sum = DB::table($type)->where('OutStcNo', $order['OutStcNo'])->sum('AdjustQnty');
                    $id =  DB::table($type)->where('OutStcNo', $order['OutStcNo'])->pluck('id');
                    $number = $model::whereIn('related_id', $id)->sum('number');
                    if ($sum == $number) {
                        DB::table($type)->where('OutStcNo', $order['OutStcNo'])->update(['state' => '已入库']);
                    } else {
                        DB::table($type)->where('OutStcNo', $order['OutStcNo'])->update(['state' => '入库中']);
                    }
                }
                if ($type == 'move_in_dirt') {
                    $sum = DB::table($type)->where('InStcNo', $order['InStcNo'])->sum('MovAdmQnty');
                    $id =  DB::table($type)->where('InStcNo', $order['InStcNo'])->pluck('id');
                    $number = $model::whereIn('related_id', $id)->sum('number');
                    if ($sum == $number) {
                        DB::table($type)->where('InStcNo', $order['InStcNo'])->update(['state' => '已入库']);
                    } else {
                        DB::table($type)->where('InStcNo', $order['InStcNo'])->update(['state' => '入库中']);
                    }
                }
                if ($type == 'ret_in_dirt') {
                    $sum = DB::table($type)->where('InStcNo', $order['InStcNo'])->sum('AdmQnty');
                    $id =  DB::table($type)->where('InStcNo', $order['InStcNo'])->pluck('id');
                    $number = $model::whereIn('related_id', $id)->sum('number');
                    if ($sum == $number) {
                        DB::table($type)->where('InStcNo', $order['InStcNo'])->update(['state' => '已入库']);
                    } else {
                        DB::table($type)->where('InStcNo', $order['InStcNo'])->update(['state' => '入库中']);
                    }
                }

                $odd = '';
                if ($type == 'ret_in_dirt') {
                    $odd = $order['OrderNo'];
                }
                if ($type == 'prod_imp') {
                    $odd = $order['InvoiceNo'];
                }
                if ($type == 'asn') {
                    $odd = $order['CustomerOrderNo'];
                }
                if ($type == 'move_in_dirt') {
                    $odd = $order['MoveNo'];
                }
                if ($type == 'adj_out_dirt') {
                    $odd = $order['AdjustNo'];
                }

                // 插入goods表和副表
                $goodsData = [
                    'product_id' => $order['product']['id'],
                    'stock_no' => $order['act_stock_no'],
                    'state_name' => $order['act_state_name'],
                    'CHARG' => empty($order['act_CHARG']) ? null : $order['act_CHARG'],
                    'available_time' => date('Ym', strtotime($order['act_available_time'])),
                ];
                $flag = DB::table('goods_tag')->where('odd', $odd)->pluck('goods_id')->toArray();
                $goods = Goods::where($goodsData)->whereIn('id', $flag)->first();
                if ($goods) {
                    Log::info('找到同一条件加工完成的商品' . $goods . '增加' . $order['act_number']);
                    $goods->number += $order['act_number'];
                    $goods->save();
                } else {
                    $goodsData['number'] = $order['act_number'];
                    $goodsData['created_at'] = $goodsData['updated_at'] = date('Y-m-d H:i:s');
                    Log::info('插入加工完成的商品' . json_encode($goodsData));
                    $res = Goods::insertGetId($goodsData);
                    DB::table('goods_tag')->insert(['goods_id' => $res, 'odd' => $odd]);
                }
                if ($type == 'ret_in_dirt') {
                    $goodsData['odd'] = $order['OrderNo'];
                }
                if ($type == 'prod_imp') {
                    $goodsData['odd'] = $order['InvoiceNo'];
                }
                if ($type == 'asn') {
                    $goodsData['odd'] = $order['CustomerOrderNo'];
                }
                if ($type == 'move_in_dirt') {
                    $goodsData['odd'] = $order['MoveNo'];
                }
                if ($type == 'adj_out_dirt') {
                    $goodsData['odd'] = $order['AdjustNo'];
                }
                $goodsData['type'] = $type;
                $goodsData['related_id'] = $order['id'];
                $goodsRecord = GoodsRecord::where($goodsData)->first();
                if ($goodsRecord) {
                    $goodsRecord->number += $order['act_number'];
                    $goodsRecord->save();
                } else {
                    $goodsData['number'] = $order['act_number'];
                    $goodsData['created_at'] = date('Y-m-d H:i:s');
                    $goodsData['updated_at'] = date('Y-m-d H:i:s');
                    GoodsRecord::insert($goodsData);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return null;
    }

    protected function inConfirm(array $params, $model, $type, $username)
    {
        $name = date('Y-m-d');
        Log::useFiles(storage_path('logs/Inreturn/In' . $name . '.log'));
        if (empty($params)) {
            return '未选择确认商品！';
        }
        $oid = [];
        foreach ($params as $item) {
            if (empty($item['act_state_name'])) return '状态必选！';
            if (empty($item['act_number'])) return '数量必填！';

            if ($type == 'prod_imp') {
                $oid[] =  DB::table($type)->where('InvoiceNo', $item['conversion'])->where('NewProductCd', $item['NewPRODUCTCD'])->pluck('id');
            }
            if ($type == 'asn') {
                $oid[] =  DB::table($type)->where('CustomerOrderNo', $item['conversion'])->where('NewProductCD', $item['NewPRODUCTCD'])->pluck('id');
            }
            if ($type == 'move_in_dirt') {
                $oid[] =  DB::table($type)->where('MoveNo', $item['conversion'])->where('NewProductCd', $item['NewPRODUCTCD'])->pluck('id');
            }
            if ($type == 'adj_out_dirt') {
                $oid[] =  DB::table($type)->where('AdjustNo', $item['conversion'])->where('NewProductCd', $item['NewPRODUCTCD'])->pluck('id');
            }
            if ($type == 'ret_in_dirt') {
                $oid[] =  DB::table($type)->where('OrderNo', $item['conversion'])->where('NewProductCd', $item['NewPRODUCTCD'])->pluck('id');
            }
            //检查入库单状态是否发生变化
            // $tag = $model::where([
            //     ['related_id', '=', $oid],
            //     ['product_id', '=', $item['product_id']]
            // ])->first();
        }

        try {
            // 开启事务
            DB::beginTransaction();
            // 循环库位
            foreach ($params as $order) {
                if($type == 'asn'){
                    $order['act_state_name'] = $order['act_state_name'];
                }
                if($type == 'move_in_dirt'){
                    $moves = DB::table($type)->where(['MoveNo'=>$order['conversion']])->first();
                    $order['act_state_name'] = $moves->EMPST;
                }
                //记录日志
                Log::info('入库类型：' . $type . '入库人：' . $username . '单号：' . $order['conversion'] . '新产品代码：' . $order['NewPRODUCTCD'] . '库位：' . $order['stock_no'] . '数量：' . $order['act_number'] . '状态：' . $order['act_state_name']);
                //插入tag表
                $tag = $model::where([
                    ['product_id', '=', $order['product_id']]
                ])->whereIn('related_id', $oid)->first();
                $tag->confirmNum = $tag->confirmNum + $order['act_number'];
                if ($tag->confirmNum > $tag->number) {
                    return '回传数量大于入库数量';
                }
                $tag->save();
                $gather = [
                    'PRODUCTCD'=>$order['PRODUCTCD'],
                    'PRODCHINM'=>$order['PRODCHINM'],
                    'number'=>$order['act_number'],
                    'state_name'=>$order['act_state_name'],
                    'odd'=>$order['conversion'],
                    'created_at' =>date('Y-m-d H:i:s')
                ];

                if ($type == 'prod_imp') {
                    $sum = DB::table($type)->where('InvoiceNo', $order['conversion'])->sum('ImportQnty');
                    $id =  DB::table($type)->where('InvoiceNo', $order['conversion'])->pluck('id');
                    $confirmNum = $model::whereIn('related_id', $id)->sum('confirmNum');
                    if ($sum == $confirmNum) {
                        DB::table($type)->where('InvoiceNo', $order['conversion'])->update(['state' => '已回传']);
                    }
                }
                if ($type == 'asn') {
                    $sum = DB::table($type)->where('CustomerOrderNo', $order['conversion'])->sum('InQnty');
                    $id =  DB::table($type)->where('CustomerOrderNo', $order['conversion'])->pluck('id');
                    $confirmNum = $model::whereIn('related_id', $id)->sum('confirmNum');
                    if ($sum == $confirmNum) {
                        DB::table($type)->where('CustomerOrderNo', $order['conversion'])->update(['state' => '已回传']);
                    }
                }
                if ($type == 'move_in_dirt') {
                    $sum = DB::table($type)->where('MoveNo', $order['conversion'])->sum('MovAdmQnty');
                    $id =  DB::table($type)->where('MoveNo', $order['conversion'])->pluck('id');
                    $confirmNum = $model::whereIn('related_id', $id)->sum('confirmNum');
                    if ($sum == $confirmNum) {
                        DB::table($type)->where('MoveNo', $order['conversion'])->update(['state' => '已回传']);
                    }
                }
                if ($type == 'adj_out_dirt') {
                    $sum = DB::table($type)->where('AdjustNo', $order['conversion'])->sum('AdjustQnty');
                    $id =  DB::table($type)->where('AdjustNo', $order['conversion'])->pluck('id');
                    $confirmNum = $model::whereIn('related_id', $id)->sum('confirmNum');
                    $FineFlg = DB::table($type)->where('AdjustNo', $order['conversion'])->pluck('FineFlg');
                    $order['act_state_name'] = $FineFlg[0];
                    if ($sum == $confirmNum) {
                        DB::table($type)->where('AdjustNo', $order['conversion'])->update(['state' => '已回传']);
                    }
                }
                if ($type == 'ret_in_dirt') {
                    $sum = DB::table($type)->where('OrderNo', $order['conversion'])->sum('AdmQnty');
                    $id =  DB::table($type)->where('OrderNo', $order['conversion'])->pluck('id');
                    $confirmNum = $model::whereIn('related_id', $id)->sum('confirmNum');
                    $FineFlg = DB::table($type)->where('OrderNo', $order['conversion'])->pluck('FineFlg');
                    $order['act_state_name'] = $FineFlg[0];
                    if ($sum == $confirmNum) {
                        DB::table($type)->where('OrderNo', $order['conversion'])->update(['state' => '已回传']);
                    }
                }
                DB::table('in_gather')->insert($gather);
                $goods_id = DB::table('goods_tag')->where('odd', $order['conversion'])->pluck('goods_id');
                // goods表和副表
                $goodsData = [
                    'product_id' => $order['product_id'],
                    'state_name' => '加工完成',
                    'stock_no' => $order['stock_no'],
                    'available_time' => $order['available_time'],
                ];

                $total = $order['act_number'];
                $act = $order['act_number'];
                $goodsRecord = GoodsRecord::where($goodsData)->where('odd', $order['conversion'])->get();
                foreach ($goodsRecord as $recode) {
                    if ($act <= 0) break;
                    if ($recode->number <= $act) {
                        $code = [
                            'product_id' => $recode->product_id,
                            'stock_no' => $recode->stock_no,
                            'state_name' => $order['act_state_name'],
                            'CHARG' => $recode->CHARG,
                            'available_time' => $recode->available_time,
                            'type' => $recode->type,
                            'odd' => $recode->odd,
                            'related_id' => $recode->related_id,
                        ];
                        $code = GoodsRecord::where($code)->first();

                        if ($code) {
                            $code->number += $act;
                            $code->save();
                            $act = 0;
                            $recode->delete();
                        } else {
                            $act -= $recode->number;
                            $recode->state_name = $order['act_state_name'];
                            $recode->save();
                        }
                    } else {
                        $recode->number -= $act;
                        $recode->save();
                        $code = [
                            'product_id' => $recode->product_id,
                            'stock_no' => $recode->stock_no,
                            'state_name' => $order['act_state_name'],
                            'CHARG' => $recode->CHARG,
                            'available_time' => $recode->available_time,
                            'type' => $recode->type,
                            'odd' => $recode->odd,
                            'related_id' => $recode->related_id,
                        ];
                        $code = GoodsRecord::where($code)->first();
                        if ($code) {
                            $code->number += $act;
                            $code->save();
                            $act = 0;
                        } else {
                            $arr = [
                                'product_id' => $recode->product_id,
                                'stock_no' => $recode->stock_no,
                                'state_name' => $order['act_state_name'],
                                'CHARG' => $recode->CHARG,
                                'number' => $act,
                                'available_time' => $recode->available_time,
                                'type' => $recode->type,
                                'odd' => $recode->odd,
                                'related_id' => $recode->related_id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            GoodsRecord::insert($arr);
                            $act = 0;
                        }
                    }
                }
                $goodsList = Goods::where($goodsData)->whereIn('id', $goods_id)->get();
                foreach ($goodsList as $goods) {
                    if ($total <= 0) break;
                    if ($goods->number <= $total) {
                        $list = [
                            'product_id' => $goods->product_id,
                            'stock_no' => $goods->stock_no,
                            'state_name' => $order['act_state_name'],
                            'CHARG' => $goods->CHARG,
                            'available_time' => $goods->available_time,
                        ];
                        $list = Goods::where($list)->first();
                        if ($list) {
                            Log::info('找到同一条件的商品' . $list . '增加' . $total);
                            $list->number += $total;
                            $list->save();
                            $total = 0;
                            Log::info('删除' . $goods);
                            $goods->delete();
                        } else {
                            Log::info('更改goods的状态' . $goods . '新的状态是' . $order['act_state_name']);
                            $total -= $goods->number;
                            $goods->state_name = $order['act_state_name'];
                            $goods->save();
                        }
                    } else {
                        Log::info('加工完成的数据：' . $goods . '减少' . $total);
                        $goods->number -= $total;
                        $goods->save();
                        $list = [
                            'product_id' => $goods->product_id,
                            'stock_no' => $goods->stock_no,
                            'state_name' => $order['act_state_name'],
                            'CHARG' => $goods->CHARG,
                            'available_time' => $goods->available_time,
                        ];
                        $list = Goods::where($list)->first();
                        if ($list) {
                            Log::info('找到同一条件的商品' . $list . '增加' . $total);
                            $list->number += $total;
                            $list->save();
                            $total = 0;
                        } else {
                            $arr = [
                                'product_id' => $goods->product_id,
                                'stock_no' => $goods->stock_no,
                                'state_name' => $order['act_state_name'],
                                'CHARG' => $goods->CHARG,
                                'number' => $total,
                                'available_time' => $goods->available_time,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            Log::info('没有找到同一条件的商品，进行插入' . json_encode($arr));
                            Goods::insert($arr);
                            $total = 0;
                        }
                    }
                }
            }
            // 生成TXT和插入
            $res = $this->inEnsure($type, $params);
            if ($res) throw new \Exception($res);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return null;
    }

    private function inEnsure($type, $params)
    {
        $data = [];
        switch ($type) {
            case 'move_in_dirt':
                foreach ($params as $order) {
                    if($type == 'move_in_dirt'){
                        $moves = DB::table($type)->where(['MoveNo'=>$order['conversion']])->first();
                        $order['act_state_name'] = $moves->EMPST;
                    }
                    $data[$order['conversion']][] = $order;
                }
                $res = $this->insertIntoOther($type, $data);
                break;
            case 'prod_imp':
                foreach ($params as $order) {
                    $data[$order['conversion']][] = $order;
                }
                $res = $this->insertIntoOther($type, $data);
                break;
            case 'asn':
                foreach ($params as $order) {
                    // $result = Location::where(['stock_no'=> $order['stock_no']])->first();
                    // if(!$result['stock_state_id']){
                    //     $order['act_state_name'] = 'C101';
                    // }else{
                    //     $local = Location::where(['stock_no'=> $order['stock_no']])->with('area','state')->first();
                    //     $order['act_state_name'] = $local->state->state_name;
                    // }
                    $data[$order['conversion']][] = $order;
                }
                $res = $this->insertIntoImport($type, $data);
                break;
            case 'ret_in_dirt':
                foreach ($params as $order) {
                    $data[$order['conversion']][] = $order;
                }
                $res = $this->insertIntoOut($type, $data);
                break;
            case 'adj_out_dirt':
                foreach ($params as $order) {
                    $data[$order['conversion']][] = $order;
                }
                $res = $this->insertIntoOut($type, $data);
                break;
        }

        return $res;
    }

    private function genTxt($type, $data)
    {
        $path = storage_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'OUT' . DIRECTORY_SEPARATOR;
        switch ($type) {
            case 'move_in_dirt':
                $this->genOtherTxt($type, $data, $path);
                break;
            case 'prod_imp':
                $this->genOtherTxt($type, $data, $path);
                break;
            case 'asn':
                $this->genImportTxt($type, $data, $path);
                break;
            case 'ret_in_dirt':
                $InStcNo = DB::table($type)->where('OrderNo', $data[0]['conversion'])->pluck('InStcNo')->toArray();
                $this->genOutTxt($type, $data, $path, $InStcNo[0]);
                break;
            case 'adj_out_dirt':
                $OutStcNo = DB::table($type)->where('AdjustNo', $data[0]['conversion'])->pluck('OutStcNo')->toArray();
                $this->genOutTxt($type, $data, $path, $OutStcNo[0]);
                break;
        }
        return null;
    }

    private function genOutTxt($type, $data, $path, $name)
    {
        if (Cache::get('generate_file_out') == 1) {
            $time = date('YmdHis', strtotime('+1second'));
        } else {
            Cache::put('generate_file_out', 1, 0.1);
            $time = date('YmdHis');
        }
        $fileName = 'OrdOutEnsure7858' . $time . $name . '.txt';
        $location = $path . $fileName;
        foreach ($data as $value) {
            $item = $this->getOutData($type, $value);
            if (!file_exists($location)) {
                file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
            }
            file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
        }
        $okName = 'LO0067858' . $time . '.ok';
        file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
    }

    private function genImportTxt($type, $data, $path)
    {
        if (Cache::get('generate_file_imp') == 1) {
            $time = date('YmdHis', strtotime('+1second'));
        } else {
            Cache::put('generate_file_imp', 1, 0.1);
            $time = date('YmdHis');
        }
        $fileName = 'ProdImpEnsureImport7858' . $time . '.txt';
        $location = $path . $fileName;
        foreach ($data as $value) {
            $item = $this->getImportData($type, $value);
            if (!file_exists($location)) {
                file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
            }
            file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
        }
        $okName = 'LO0047858' . $time . '.ok';
        file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
    }

    private function genOtherTxt($type, $data, $path)
    {
        if (Cache::get('generate_file_other') == 1) {
            $time = date('YmdHis', strtotime('+1second'));
        } else {
            Cache::put('generate_file_other', 1, 0.1);
            $time = date('YmdHis');
        }
        $fileName = 'ProdImpEnsureOther7858' . $time . '.txt';
        $location = $path . $fileName;
        foreach ($data as $value) {
            $item = $this->getOtherData($type, $value);
            if (!file_exists($location)) {
                file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
            }
            file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
        }
        $okName = 'LO0047858' . $time . '.ok';
        file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
    }

    private function insertIntoOther($type, $params)
    {
        switch ($type) {
            case 'move_in_dirt':
                foreach ($params as $val) {
                    $last_names = array_column($val, 'product_id');
                    array_multisort($last_names, SORT_ASC, $val);
                    $pre = $prf = [];
                    $pre_key = '';
                    foreach ($val as $k => $info) {
                        $key = $info['product_id'] . '-' . $info['act_state_name'];
                        if ($k === 0) {
                            $pre[$key] = $info;
                            $pre_key = $key;
                            continue;
                        }
                        if (array_key_exists($key, $pre)) {
                            $pre[$key]['act_number'] += $info['act_number'];
                        } else {
                            $prf[] = $pre[$pre_key];
                            $pre = [];
                            $pre_key = $key;
                            $pre[$key] = $info;
                        }
                    }
                    //最后一条特殊处理
                    $prf[] = $pre[$pre_key];
                    $result = $this->genTxt($type, $prf);
                    foreach ($prf as $order) {
                        $data = $this->getOtherData($type, $order);
                        $data['created_at'] = date('Y-m-d H:i:s');
                        ProdEnsureOther::insert($data);
                    }
                }
                break;
            case 'prod_imp':
                foreach ($params as $val) {
                    $last_names = array_column($val, 'product_id');
                    array_multisort($last_names, SORT_ASC, $val);
                    $pre = $prf = [];
                    $pre_key = '';
                    foreach ($val as $k => $info) {
                        $key = $info['product_id'] . '-' . $info['act_state_name'];
                        if ($k === 0) {
                            $pre[$key] = $info;
                            $pre_key = $key;
                            continue;
                        }
                        if (array_key_exists($key, $pre)) {
                            $pre[$key]['act_number'] += $info['act_number'];
                        } else {
                            $prf[] = $pre[$pre_key];
                            $pre = [];
                            $pre_key = $key;
                            $pre[$key] = $info;
                        }
                    }
                    //最后一条特殊处理
                    $prf[] = $pre[$pre_key];
                    $result = $this->genTxt($type, $prf);
                    foreach ($prf as $order) {
                        $data = $this->getOtherData($type, $order);
                        $data['created_at'] = date('Y-m-d H:i:s');
                        ProdEnsureOther::insert($data);
                    }
                }
                break;
        }

        return $result;
    }

    private function insertIntoOut($type, $params)
    {
        foreach ($params as $val) {
            $last_names = array_column($val, 'product_id');
            array_multisort($last_names, SORT_ASC, $val);
            $pre = $prf = [];
            $pre_key = '';
            foreach ($val as $k => $info) {
                $key = $info['product_id'];
                if ($k === 0) {
                    $pre[$key] = $info;
                    $pre_key = $key;
                    continue;
                }
                if (array_key_exists($key, $pre)) {
                    $pre[$key]['act_number'] += $info['act_number'];
                } else {
                    $prf[] = $pre[$pre_key];
                    $pre = [];
                    $pre_key = $key;
                    $pre[$key] = $info;
                }
            }
            //最后一条特殊处理
            $prf[] = $pre[$pre_key];
            $result = $this->genTxt($type, $prf);
            foreach ($prf as $order) {
                $data = $this->getOutData($type, $order);
                // $data['created_at'] = date('Y-m-d H:i:s');
                // OrderOutEnsure::insert($data);
            }
        }

        return $result;
    }

    private function getOutData($type, $order)
    {
        $data = [];
        switch ($type) {
            case 'ret_in_dirt':
                $res =  DB::table($type)->where('OrderNo', $order['conversion'])->where('NewProductCD', $order['NewPRODUCTCD'])->get();
                $data = [
                    'VBELN' => $res[0]->InStcNo,
                    'ISDD' => date('Ymd'),
                    'POSNR' => $res[0]->LineNo,
                    'MATNR' => $order['NewPRODUCTCD'],
                    'AdmQnty' => $order['act_number'],
                    'OrdTyep' => $res[0]->AUART,
                    'CHARG' => '00',
                    'WERKS' => '7858',
                    'LGORT' => $res[0]->FineFlg,
                    'PRODUCTCD' => $order['PRODUCTCD'],
                ];
                break;
            case 'adj_out_dirt':
                $res =  DB::table($type)->where('AdjustNo', $order['conversion'])->where('NewProductCD', $order['NewPRODUCTCD'])->get();
                $data = [
                    'VBELN' => $res[0]->OutStcNo,
                    'ISDD' => date('Ymd'),
                    'POSNR' => $res[0]->LineNo,
                    'MATNR' => $order['NewPRODUCTCD'],
                    'AdmQnty' => $order['act_number'],
                    'OrdTyep' => $res[0]->AUART,
                    'CHARG' => '00',
                    'WERKS' => '7858',
                    'LGORT' => $res[0]->FineFlg,
                    'PRODUCTCD' => $order['PRODUCTCD'],
                ];
                break;
        }
        return $data;
    }

    private function getOtherData($type, $order)
    {
        $data = [];
        switch ($type) {
            case 'move_in_dirt':
                $res =  DB::table($type)->where('MoveNo', $order['conversion'])->where('NewProductCD', $order['NewPRODUCTCD'])->get();
                $data = [
                    'BLDAT' => date('Ymd', strtotime($res[0]->created_at)),
                    'BUDAT' => date('Ymd'),
                    'BKTXT' => '',
                    'MATNR' => $order['NewPRODUCTCD'],
                    'WERKS' => '7858',
                    'LGORT' => $order['act_state_name'],
                    'CHARG' => '00',
                    'ERFMG' => $order['act_number'],
                    'ERFME' => 'EA',
                    'EBELN' => $res[0]->MoveNo,
                    'EBELP' => $res[0]->POSNR,
                    'SGTXT' => '',
                    'UMWRK' => '',
                    'POSNR' => $res[0]->LineNo,
                    'VBELN' => $res[0]->InStcNo,
                    'LIFNR' => '',
                    'BSART' => $res[0]->AUART,
                    'PRODUCTCD' => $order['PRODUCTCD'],
                ];
                break;
            case 'prod_imp':
                $res =  DB::table($type)->where('InvoiceNo', $order['conversion'])->where('NewProductCD', $order['NewPRODUCTCD'])->get();
                $data = [
                    'BLDAT' => date('Ymd', strtotime($res[0]->created_at)),
                    'BUDAT' => date('Ymd'),
                    'BKTXT' => '',
                    'MATNR' => $order['NewPRODUCTCD'],
                    'WERKS' => '7858',
                    'LGORT' => $order['act_state_name'],
                    'CHARG' => '00',
                    'ERFMG' => $order['act_number'],
                    'ERFME' => 'EA',
                    'EBELN' => $order['conversion'],
                    'EBELP' => $res[0]->POSNR,
                    'SGTXT' => '',
                    'UMWRK' => '',
                    'POSNR' => '',
                    'VBELN' => '',
                    'LIFNR' => '',
                    'BSART' => $res[0]->BSART,
                    'PRODUCTCD' => $order['PRODUCTCD'],
                ];
                break;
        }
        return $data;
    }

    private function getImportData($type, $order)
    {
        $data = [];
        $res =  DB::table($type)->where('CustomerOrderNo', $order['conversion'])->where('NewProductCD', $order['NewPRODUCTCD'])->get();
        switch ($type) {
            case 'asn':
                $data = [
                    'BLDAT' => date('Ymd', strtotime($res[0]->created_at)),
                    'BUDAT' => date('Ymd'),
                    'BATXT' => $order['conversion'],
                    'MATNR' => $order['NewPRODUCTCD'],
                    'WERKS' => '7858',
                    'LGORT' => $res[0]->LGORT,
                    'CHARG' => '00',
                    'ERFMG' => $order['act_number'],
                    'ERFME' => 'EA',
                    'EBELN' => '',
                    'EBELP' => '',
                    'SGTXT' => '',
                    'UMLGO' => $order['act_state_name'],
                    'UMCHA' => '00',
                    'POSNR' => '',
                    'VBELN' => '',
                    'LIFER' => '',
                    'BSART' => 'EL',
                    'PRODUCTCD' => $order['PRODUCTCD'],
                ];
                break;
        }
        return $data;
    }

    private function insertIntoImport($type, $params)
    {
        switch ($type) {
            case 'asn':
                foreach ($params as $val) {
                    $last_names = array_column($val, 'product_id');
                    array_multisort($last_names, SORT_ASC, $val);
                    $pre = $prf = [];
                    $pre_key = '';
                    foreach ($val as $k => $info) {
                        $key = $info['product_id'] . '-' . $info['act_state_name'];
                        if ($k === 0) {
                            $pre[$key] = $info;
                            $pre_key = $key;
                            continue;
                        }
                        if (array_key_exists($key, $pre)) {
                            $pre[$key]['act_number'] += $info['act_number'];
                        } else {
                            $prf[] = $pre[$pre_key];
                            $pre = [];
                            $pre_key = $key;
                            $pre[$key] = $info;
                        }
                    }
                    //最后一条特殊处理
                    $prf[] = $pre[$pre_key];
                    $result = $this->genTxt($type, $prf);
                    foreach ($prf as $order) {
                        $data = $this->getImportData($type, $order);
                        $data['created_at'] = date('Y-m-d H:i:s');
                        ProdEnsureImport::insert($data);
                    }
                }
                break;
        }

        return $result;
    }
}
