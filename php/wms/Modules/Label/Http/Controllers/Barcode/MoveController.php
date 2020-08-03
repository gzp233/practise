<?php

namespace Modules\Label\Http\Controllers\Barcode;

use Illuminate\Http\Request;
use Modules\Label\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Modules\Label\Models\ItemStock;

class MoveController extends BaseController
{
    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function getById(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        $support_no = $request->get('support_no');
        $logo = $request->get('logo');
        $db = DB::connection('labelDB');
        if (!$support_no ||  !$yiku_no) return $this->sendData(402, '托号或单号不能为空');
        $data = [];
        $stockData = $this->getFromRedis($yiku_no);
        // if ($stockData->state != 0) return $this->sendData(402, '已完成移出操作，无法继续移出');
        // if (isset($stockData->data->$support_no)) {
        //     foreach ($stockData->data->$support_no as $index => $item) {
        //         $product = $item->material_code;
        //         $item->expired_at = $product->expired_at;
        //         $data[$index] = $item;
        //     }
        //     $this->setToRedis($yiku_no, $stockData, 72 * 3600);
        // }
        // $checkList = [];
        // foreach ($stockData->checkList as $key => $value) {
        //     $checkList[$key] = $value;
        // }
        $result = $db->table('item_stock')->where('support_no', $support_no)->where('state', '1')->first();
        if ($result) {
            $result = $db->table('item_stock')->where('support_no', $support_no)->where('state', '1')->get();
            foreach ($result as $item) {
                if (isset($data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at])) {
                    $data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at]['num'] += $item->num;
                } else {
                    $data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at] = [
                        'id' => $item->id,
                        'num' => $item->num,
                        'invoice_no' => $item->invoice_no,
                        'item_no' => $item->item_no,
                        'material_code' => $item->material_code,
                        'item_name' => $item->item_name,
                        'location_no' => $item->location_no,
                        'support_no' => $item->support_no,
                        'box_mark' => $item->box_mark,
                        'case_mark' => $item->case_mark,
                        'branch_mark' => $item->branch_mark,
                        'valid_month' => $item->valid_month,
                        'status' => $item->status,
                        'expired_at' => $item->expired_at,
                        'is_whole' => $logo,
                        'state' => $item->state,
                        'production_date' => $item->production_date,
                        'scanNumber' => $item->num
                    ];
                }
            }
            return $this->sendData(200, '', $data);
        }
        if (!$data) {
            // 库位也要检查
            $rs = $db->table('item_stock')->where('support_no', $support_no)->where('state', '0')->get();
            if ($logo == 0) {
                foreach ($rs as $item) {
                    if (isset($data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at])) {
                        $data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at]['num'] += $item->num;
                    } else {
                        $data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at] = [
                            'id' => $item->id,
                            'num' => $item->num,
                            'invoice_no' => $item->invoice_no,
                            'item_no' => $item->item_no,
                            'material_code' => $item->material_code,
                            'item_name' => $item->item_name,
                            'location_no' => $item->location_no,
                            'support_no' => $item->support_no,
                            'box_mark' => $item->box_mark,
                            'case_mark' => $item->case_mark,
                            'branch_mark' => $item->branch_mark,
                            'valid_month' => $item->valid_month,
                            'status' => $item->status,
                            'expired_at' => $item->expired_at,
                            'is_whole' => $logo,
                            'state' => $item->state,
                            'production_date' => $item->production_date,
                            'scanNumber' => $item->num
                        ];
                    }
                }
            } else {
                foreach ($rs as $item) {
                    if (isset($data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at])) {
                        $data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at]['num'] += $item->num;
                    } else {
                        $data[$item->invoice_no . '_' . $item->location_no . '_' . $item->box_mark . '_' . $item->case_mark . '_' . $item->branch_mark . '_' . $item->material_code . '_' . $item->expired_at] = [
                            'id' => $item->id,
                            'num' => $item->num,
                            'invoice_no' => $item->invoice_no,
                            'item_no' => $item->item_no,
                            'material_code' => $item->material_code,
                            'item_name' => $item->item_name,
                            'location_no' => $item->location_no,
                            'support_no' => $item->support_no,
                            'box_mark' => $item->box_mark,
                            'case_mark' => $item->case_mark,
                            'branch_mark' => $item->branch_mark,
                            'valid_month' => $item->valid_month,
                            'status' => $item->status,
                            'expired_at' => $item->expired_at,
                            'is_whole' => $logo,
                            'state' => $item->state,
                            'production_date' => $item->production_date,
                            'scanNumber' => 0
                        ];
                    }
                }
            }

            if (empty($data)) return $this->sendData(200, '该托号不存在或无商品');
        }

        return $this->sendData(200, '', $data);
    }

    // 从redis中获取数据
    private function getFromRedis($id)
    {
        return json_decode(Redis::get('move::' . $id));
    }

    // 保存数据到redis
    private function setToRedis($id, $data, $time = 0)
    {
        if ($data) {
            if ($time) {
                Redis::setex('move::' . $id, $time, json_encode($data));
            } else {
                Redis::set('move::' . $id, json_encode($data));
            }
        } else {
            Redis::del('move::' . $id);
        }

        return true;
    }

    public function getByNo(Request $request)
    {
        $params = $request->get('params');
        $yiku_no = $request->get('yiku_no');
        $support_no = $request->get('support_no');
        $logo = $request->get('logo');
        $db = DB::connection('labelDB');
        if (!$support_no || !$yiku_no) return $this->sendData(402, '库位号或单号不能为空');
        if (!$params) return $this->sendData(402, '数据不能为空');
        // $stockData = $this->getFromRedis($yiku_no);
        // if ($stockData->status != 0) return $this->sendData(402, '已完成移出操作，无法继续移出');
        try {
            // 开启事务
            DB::beginTransaction();
            if ($logo == 0) {
                foreach ($params as $item) {
                    $data = [
                        'item_stock_id' => $item['id'],
                        'task_no' => $yiku_no,
                        'is_whole' => $logo,
                        'support_no' => $support_no,
                    ];
                    $db->table('item_stock_tag')->insert($data);
                    $db->table('item_stock')->where('id', $item['id'])->update(['state' => 1]);
                }
            } else {
                $rs = $db->table('item_stock')->where('support_no', $support_no)->where('state', '0')->get();
                $count = 0;
                foreach ($params as $key) {
                    if ($key['scanNumber'] > $key['num']) return $this->sendData(402, '扫描数量不能超过库存数量');
                    if ($key['scanNumber'] == 0) {
                        $count++;
                        continue;
                    } else {
                        $list = [
                            'invoice_no' => $key['invoice_no'],
                            'item_no' => $key['item_no'],
                            'location_no' => $key['location_no'],
                            'support_no' => $key['support_no'],
                            'box_mark' => $key['box_mark'] === null ? '' : $key['box_mark'],
                            'case_mark' => $key['case_mark'] === null ? '' : $key['case_mark'],
                            'branch_mark' => $key['branch_mark'] === null ? '' : $key['branch_mark'],
                            'expired_at' => $key['expired_at'] === null ? '' : $key['expired_at'],
                        ];
                        $rs = ItemStock::where($list)->where('state', '0')->first();
                        $rs->num -= $key['scanNumber'];
                        if ($rs->num == 0) {
                            $rs->delete();
                        } else {
                            $rs->save();
                        }
                        $lists = [
                            'num' => $key['scanNumber'],
                            'invoice_no' => $key['invoice_no'],
                            'item_no' => $key['item_no'],
                            'material_code' => $key['material_code'],
                            'item_name' => $key['item_name'],
                            'location_no' => $key['location_no'],
                            'support_no' => $key['support_no'],
                            'box_mark' => $key['box_mark'] === null ? '' : $key['box_mark'],
                            'case_mark' => $key['case_mark'] === null ? '' : $key['case_mark'],
                            'branch_mark' => $key['branch_mark'] === null ? '' : $key['branch_mark'],
                            'valid_month' => $key['valid_month'],
                            'status' => $key['status'],
                            'production_date' => $key['production_date'] === null ? '' : $key['production_date'],
                            'expired_at' => $key['expired_at'] === null ? '' : $key['expired_at'],
                            'state' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        $rc = ItemStock::insertGetId($lists);
                        $i = [
                            'item_stock_id' => $rc,
                            'task_no' => $yiku_no,
                            'is_whole' => $logo,
                            'support_no' => $support_no,
                        ];
                        $db->table('item_stock_tag')->insert($i);
                    }
                }
                if ($count == count($params)) return sendData(402, '扫描数据不能为空');
            }
            $this->setToRedis($yiku_no, null);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $this->sendData();
    }

    //手机端已扫描转出库位列表
    public function getOriginStockList(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return $this->sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        $list = [];
        if ($stockData) {
            if (isset($stockData->source) && $stockData->source != 1) return $this->sendData(402, '入口错误');
            foreach ($stockData->data as $stock_no => $item) {
                $list[] = $stock_no;
            }
            if ($stockData->state != 0) return $this->sendData(402, '状态错误！');
        }

        return $this->sendData(200, '', $list);
    }

    //手机端完成移出操作
    public function submitOrigin(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        $userId = $this->user->id;
        if (!$yiku_no) return $this->sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if (!$stockData) return $this->sendData(402, '单号错误');
        // 把扫描的数据整理出来
        if (!$stockData->data) return $this->sendData(402, '提交数据不能为空');
        try {
            DB::beginTransaction();
            foreach ($stockData->data as $support_no => $row) {
                $tmpData = [];
                $db = DB::connection('labelDB');
                $rs = $db->table('item_stock')->where('support_no', $support_no)->get();
                foreach ($rs as $v) {
                    if (!isset($tmpData[$v->material_code . '_' . $v->expired_at])) {
                        $tmpData[$v->material_code . '_' . $v->expired_at] = [
                            'data' => [$v],
                            'total' => $v->number
                        ];
                    } else {
                        $tmpData[$v->material_code . '_' . $v->expired_at]['data'][] = $v;
                        $tmpData[$v->material_code . '_' . $v->expired_at]['total'] += $v->number;
                    }
                }
                foreach ($row as $item) {
                    if ($item->scanNumber <= 0) continue;
                    $index = $item->material_code . '_' . $item->expired_at;
                    // 验证库位状态
                    if (!isset($tmpData[$index]) || $item->number != $tmpData[$index]['total']) {
                        $stockData->data = new \stdClass();
                        $stockData->checkList = [];
                        $stockData->goodsList = [];
                        $this->setToRedis($yiku_no, $stockData,  3 * 24 * 3600);
                        throw new \Exception('库存状态发生变化，请刷新页面重试');
                    }
                    // 开始分配
                    $total = $item->scanNumber;
                    foreach ($tmpData[$index]['data'] as $goods) {
                        if ($total == 0) break;
                        if ($total >= $goods->num) {
                            if ($goods->num == 0) {
                                $goods->delete();
                            } else {
                                $goods->num = 0;
                                $goods->save();
                            }
                            $total -= $goods->num;
                        } else {
                            $goods->num = $goods->num - $total;
                            $goods->save();
                            $total = 0;
                        }
                    }

                    $goodsData = [
                        'number' => $item->scanNumber,
                        'invoice_no' => $item->invoice_no,
                        'item_no' => $item->item_no,
                        'material_code' => $item->material_code,
                        'item_name' => $item->item_name,
                        'location_no' => '移库区',
                        'support_no' => $support_no,
                        'box_mark' => $item->box_mark,
                        'case_mark' => $item->case_mark,
                        'branch_mark' => $item->branch_mark,
                        'valid_month' => $item->valid_month,
                        'status' => $item->status,
                        'production_date' => $item->production_date,
                        'expired_at' => $item->expired_at,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $this->itemIn($goodsData);
                    $yikuData = [
                        'user_id' => $userId,
                        'task_no' => $yiku_no,
                        'num' => $item->scanNumber,
                        'invoice_no' => $item->invoice_no,
                        'item_no' => $item->item_no,
                        'item_name' => $item->item_name,
                        'from_location_no' => $item->from_location_no,
                        'to_location_no' => $item->to_location_no,
                        'from_support_no' => $item->from_support_no,
                        'to_support_no' => $item->to_support_no,
                        'box_mark' => $item->box_mark,
                        'case_mark' => $item->case_mark,
                        'branch_mark' => $item->branch_mark,
                        'valid_month' => $item->valid_month,
                        'status' => $item->status,
                        'production_date' => $item->production_date,
                        'expired_at' => $item->expired_at,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $db->table('item_move')->insert($yikuData);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendData(402, $e->getMessage());
        }
        $this->setToRedis($yiku_no, null);

        return $this->sendData();
    }
    //手机端已扫描转出库位列表
    public function getToStockList(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return $this->sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        $list = [];
        if ($stockData) {
            if (isset($stockData->source)) return $this->sendData(402, '入口错误');
            foreach ($stockData->data as $support_no => $item) {
                $list[] = $support_no;
            }
        } else {
            $this->setGoodsData($yiku_no);
        }
        return $this->sendData(200, '', $list);
    }

    private function setGoodsData($yiku_no)
    {
        $query = [
            ['task_no', '=', $yiku_no],
            ['status', '=', 0]
        ];
        $db = DB::connection('labelDB');
        $yikus = $db->table('item_move')->where($query)->get();
        if (!$yikus) throw new \Exception('该单不存在或者状态异常');
        $goodsList = $checkList = [];
        foreach ($yikus as $item) {
            $checkList[$item->material_code] = $item->material_code;
            if (isset($goodsList[$item->material_code . '_' . $item->expired_at])) {
                $goodsList[$item->material_code . '_' . $item->expired_at]['number'] +=  $item->number;
            } else {
                $goodsList[$item->material_code . '_' . $item->expired_at] = [
                    'material_code' => $item->material_code,
                    'available_time' => $item->expired_at,
                    'number' => $item->number,
                    'scanNumber' => 0
                ];
            }
        }
        $stockData = [
            'data' => new \stdClass(),
            'checkList' => $checkList,
            'goodsList' => $goodsList,
            'status' => 1,
            'source' => 1
        ];
        $this->setToRedis($yiku_no, $stockData);
    }


    //手机端获取已扫描的转出商品列表
    public function getGoodsList(Request $request)
    {
        $db = DB::connection('labelDB');
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return $this->sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if (!$stockData) {
            if (!$db->table('item_move')->where(['task_no' => $yiku_no, 'state' => 0])->count()) return $this->sendData(402, '获取失败');
            $this->setGoodsData($yiku_no);
            $stockData = $this->getFromRedis($yiku_no);
            if (!$stockData) return $this->sendData(402, '获取失败，单号错误');
        }
        $data = [];
        foreach ($stockData->goodsList as $index => $item) {
            $material_code = $item->material_code;
            $data[$index] = $item;
        }
        $this->setToRedis($yiku_no, $stockData);

        if ($stockData->state != 1) return $this->sendData(402, '请先进行转出操作！');

        return $this->sendData(200, '', $data);
    }

    //手机端保存转出库位扫码
    public function stockTo(Request $request)
    {
        $material_code = $request->get('material_code');
        $yiku_no = $request->get('yiku_no');
        $params = $request->get('params');
        if (!$material_code || !$yiku_no) return $this->sendData(402, '库位号或单号不能为空');
        if (!$params) return $this->sendData(402, '数据不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if ($stockData->status == 0) return $this->sendData(402, '未完成移出操作');
        $stockList = [];
        if (isset($stockData->data->$material_code)) {
            foreach ($stockData->data->$material_code as $key => $item) {
                $stockList[$key] = $item;
            }
        }

        $scanCount = 0;
        foreach ($params as $item) {
            $key = $item['material_code'] . '_' . $item['expired_at'];
            $stockData->goodsList->$key->scanNumber += $item['scanNumber'] - $item['before'];
            if ($stockData->goodsList->$key->scanNumber > $stockData->goodsList->$key->number) {
                return $this->sendData(402, '扫描数量不能超过移出数量');
            }
            $stockData->goodsList->$key->before = $item['scanNumber'];
            if (isset($stockList[$key])) {
                $stockList[$key]->scanNumber += $item['scanNumber'] - $item['before'];
                $scanCount += $stockList[$key]->scanNumber;
                if ($stockList[$key]->scanNumber == 0) unset($stockList[$key]);
            } else {
                $stockList[$key] = $item;
                $stockList[$key]['scanNumber'] = $item['scanNumber'] - $item['before'];
                $scanCount += $stockList[$key]['scanNumber'];
                if ($stockList[$key]['scanNumber'] == 0) unset($stockList[$key]);
            }
        }
        if ($scanCount == 0) {
            unset($stockData->data->$material_code);
        } else {
            $stockData->data->$material_code = $stockList;
        }

        $this->setToRedis($yiku_no, $stockData);

        return $this->sendData();
    }

    //手机端提交移入库位
    public function submitTo(Request $request)
    {
        $db = DB::connection('labelDB');
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return $this->sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if (!$stockData) return $this->sendData(402, '单号错误');
        if ($stockData->state != 1) return $this->sendData(402, '状态错误,移出操作未完成');
        // 把扫描的数据整理出来
        if (!$stockData->data) return $this->sendData(402, '提交数据不能为空');
        // 先验证是否全部扫描完成
        foreach ($stockData->goodsList as $item) {
            if ($item->scanNumber != $item->number) return $this->sendData(402, '未移入全部商品');
        }

        //下面先整理数据，库位对应
        $yikuData = [];
        $query = [
            'task_no' => $yiku_no,
            'source' => 1
        ];
        $res = $db->table('item_move')->where($query)->get();
        if (!$res) return $this->sendData(402, '移库单发生变化，请刷新重试');
        foreach ($res as $v) {
            $yikuData[$v->material_code . '_' . $v->expired_at][] = $v;
        }
        try {
            DB::beginTransaction();
            $db->table('item_stock')->where(['support_no' => '移库区'])->whereIn('invoice_no', $yiku_no)->delete();
            foreach ($stockData->data as $support_no => $row) {
                // 检查是否异常
                //  $stockCheckList = [];
                //  $rs = $db->table('item_stock')->where('support_no', $support_no)->get();
                //  foreach ($rs as $v) {
                //      if (in_array($v->product_id, array_keys($stockCheckList))) {
                //          if ($stockCheckList[$v->product_id] != $v->state_name) return $this->sendData(402, '库位异常,存在不同状态商品');
                //      } else {
                //          $stockCheckList[$v->product_id] = $v->state_name;
                //      }
                //  }
                foreach ($row as $item) {
                    if ($item->scanNumber == 0) continue;
                    $goodsData = [
                        'product_id' => $item->product_id,
                        'state_name' => $item->state_name,
                        'CHARG' => '00',
                        'number' => $item->scanNumber,
                        'available_time' => $item->available_time,
                        'real_number' => $item->scanNumber,
                        'frozen_number' => 0,
                        'support_no' => $support_no
                    ];
                    $rc = Goods::create($goodsData);
                    $other = [
                        'support_no' => '移库区',
                        'type' => 'yiku',
                        'available_time' => $item->available_time,
                        'related_id' => 1,
                    ];
                    $goodsRecordData = array_merge($goodsData, $other);
                    unset($goodsRecordData['real_number'], $goodsRecordData['frozen_number']);
                    $index = $item->product_id . '_' . $item->available_time;
                    // 开始分配
                    $total = $item->scanNumber;
                    foreach ($yikuData[$index] as $k => $yiku) {
                        if ($total == 0) break;
                        if ($total >= $yiku->number) {
                            $yiku->delete();
                            unset($yikuData[$index][$k]);
                            $total -= $yiku->number;
                        } else {
                            $yiku->number = $yiku->number - $total;
                            $yiku->save();
                            $total = 0;
                        }

                        $yikuInsertData = [
                            'yiku_no' => $yiku_no,
                            'create_user' => $this->user->id,
                            'goods_id' => $rc->id,
                            'deal_user' => $this->user->id,
                            'product_id' => $item->product_id,
                            'origin_stock_no' => $yiku->origin_stock_no,
                            'origin_state_name' => $yiku->origin_state_name,
                            'origin_available_time' => $yiku->origin_available_time,
                            'stock_no' => $support_no,
                            'state_name' => $item->state_name,
                            'available_time' => $item->available_time,
                            'starttime' => $yiku->starttime,
                            'endtime' => date('Y-m-d H:i:s'),
                            'number' => $item->scanNumber,
                            'source' => 1,
                            'status' => 2
                        ];
                        Yiku::create($yikuInsertData);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendData(402, $e->getMessage());
        }
        $this->setToRedis($yiku_no, null);

        return $this->sendData();
    }

    //手机端发起任务
    public function createTask(Request $request)
    {
        $yiku_no = date('YmdHis') . rand(10, 99);
        $data = [
            'data' => new \stdClass(),
            'starttime' => date('Y-m-d H:i:s'),
            'checkList' => [],
            'goodsList' => [],
            'status' => 0
        ];
        $this->setToRedis($yiku_no, $data, 3 * 24 * 3600);

        return $this->sendData(200, '', $yiku_no);
    }


    public function submitWhole(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        $support_no = $request->get('support_no');
        $location_no = $request->get('location_no');
        $logo = $request->get('logo');
        if (!$support_no || !$yiku_no || !$location_no) return $this->sendData(402, '库位号或单号或托号不能为空');
        $db = DB::connection('labelDB');
        $tag = $db->table('item_stock_tag')->where('task_no', $yiku_no)->pluck('item_stock_id');
        $res = $db->table('item_stock')->whereIn('id', $tag)->get();
        try {
            // 开启事务
            DB::beginTransaction();
            if (count($res) == 0) {
                return $this->sendData(402, '该单号没有需要移动的商品');
            }
            if ($location_no == '暂存区') {
                foreach ($res as $item) {
                    $list = [
                        'user_id' => $this->user->id,
                        'task_no' => $yiku_no,
                        'num' => $item->num,
                        'invoice_no' => $item->invoice_no,
                        'item_no' => $item->item_no,
                        'material_code' => $item->material_code,
                        'item_name' => $item->item_name,
                        'from_location_no' => $item->location_no,
                        'to_location_no' => $location_no,
                        'from_support_no' => $item->support_no,
                        'to_support_no' => $support_no,
                        'box_mark' => $item->box_mark,
                        'case_mark' => $item->case_mark,
                        'branch_mark' => $item->branch_mark,
                        'valid_month' => $item->valid_month,
                        'status' => $item->status,
                        'state' => $item->state,
                        'is_whole' => $logo,
                        'production_date' => $item->production_date,
                        'expired_at' => $item->expired_at,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $result = [
                        'state' => 0,
                        'location_no' => $location_no,
                    ];
                    $item_stock_id = $db->table('item_stock_tag')->where('task_no', $yiku_no)->pluck('item_stock_id');
                    $db->table('item_stock')->whereIn('id', $item_stock_id)->update($result);
                    $db->table('item_move')->insert($list);
                }
            } else {
                $support = $db->table('item_stock')->where('location_no', $location_no)->first();
                if (!$support) {
                    foreach ($res as $item) {
                        $list = [
                            'user_id' => $this->user->id,
                            'task_no' => $yiku_no,
                            'num' => $item->num,
                            'invoice_no' => $item->invoice_no,
                            'item_no' => $item->item_no,
                            'material_code' => $item->material_code,
                            'item_name' => $item->item_name,
                            'from_location_no' => $item->location_no,
                            'to_location_no' => $location_no,
                            'from_support_no' => $item->support_no,
                            'to_support_no' => $support_no,
                            'box_mark' => $item->box_mark,
                            'case_mark' => $item->case_mark,
                            'branch_mark' => $item->branch_mark,
                            'valid_month' => $item->valid_month,
                            'status' => $item->status,
                            'state' => $item->state,
                            'is_whole' => $logo,
                            'production_date' => $item->production_date,
                            'expired_at' => $item->expired_at,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        $result = [
                            'state' => 0,
                            'location_no' => $location_no,
                        ];
                        $item_stock_id = $db->table('item_stock_tag')->where('task_no', $yiku_no)->pluck('item_stock_id');
                        $db->table('item_stock')->whereIn('id', $item_stock_id)->update($result);
                        $db->table('item_move')->insert($list);
                    }
                } else {
                    if ($support->support_no == $support_no) {
                        foreach ($res as $item) {
                            $list = [
                                'user_id' => $this->user->id,
                                'task_no' => $yiku_no,
                                'num' => $item->num,
                                'invoice_no' => $item->invoice_no,
                                'item_no' => $item->item_no,
                                'material_code' => $item->material_code,
                                'item_name' => $item->item_name,
                                'from_location_no' => $item->location_no,
                                'to_location_no' => $location_no,
                                'from_support_no' => $item->support_no,
                                'to_support_no' => $support_no,
                                'box_mark' => $item->box_mark,
                                'case_mark' => $item->case_mark,
                                'branch_mark' => $item->branch_mark,
                                'valid_month' => $item->valid_month,
                                'status' => $item->status,
                                'state' => $item->state,
                                'is_whole' => $logo,
                                'production_date' => $item->production_date,
                                'expired_at' => $item->expired_at,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            $item_stock_id = $db->table('item_stock_tag')->where('task_no', $yiku_no)->pluck('item_stock_id');
                            $result = [
                                'state' => 0,
                                'location_no' => $location_no,
                            ];
                            $db->table('item_stock')->whereIn('id', $item_stock_id)->update($result);
                            $db->table('item_move')->insert($list);
                        }
                    } else {
                        return $this->sendData(402, '该库位上有其他托');
                    }
                }
            }
            $db->table('item_stock_tag')->where('task_no', $yiku_no)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $this->sendData(200, '移动完成');
    }
    public function submitPort(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        $support_no = $request->get('old_support_no');
        $new_support_no = $request->get('new_support_no');
        $logo = $request->get('logo');
        if (!$support_no || !$yiku_no || !$new_support_no) return $this->sendData(402, '库位号或单号或托号不能为空');
        $db = DB::connection('labelDB');
        $tag = $db->table('item_stock_tag')->where('task_no', $yiku_no)->pluck('item_stock_id');
        $res = $db->table('item_stock')->whereIn('id', $tag)->get();
        try {
            // 开启事务
            DB::beginTransaction();
            if (count($res) == 0) {
                return $this->sendData(402, '该单号没有需要移动的商品');
            }
            $data = $db->table('item_stock')->where('support_no',$new_support_no)->first();
            $location_no = '暂存区';
            if($data){
                $location_no = $data->location_no;
            }
            foreach($res as $key){
                $arr = [
                    'num' => $key->num,
                    'invoice_no' => $key->invoice_no,
                    'item_no' => $key->item_no,
                    'material_code' => $key->material_code,
                    'item_name' => $key->item_name,
                    'location_no' => $location_no,
                    'support_no' => $new_support_no,
                    'box_mark' => $key->box_mark,
                    'case_mark' => $key->case_mark,
                    'branch_mark' => $key->branch_mark,
                    'valid_month' => $key->valid_month,
                    'status' => $key->status,
                    'production_date' => $key->production_date,
                    'expired_at' => $key->expired_at,
                    'state' => 0,
                ];
                $this->itemIn($arr);
                $list = [
                    'user_id' => $this->user->id,
                    'task_no' => $yiku_no,
                    'num' => $key->num,
                    'invoice_no' => $key->invoice_no,
                    'item_no' => $key->item_no,
                    'material_code' => $key->material_code,
                    'item_name' => $key->item_name,
                    'from_location_no' => $key->location_no,
                    'to_location_no' => $location_no,
                    'from_support_no' => $key->support_no,
                    'to_support_no' => $new_support_no,
                    'box_mark' => $key->box_mark,
                    'case_mark' => $key->case_mark,
                    'branch_mark' => $key->branch_mark,
                    'valid_month' => $key->valid_month,
                    'status' => $key->status,
                    'state' => $key->state,
                    'is_whole' => $logo,
                    'production_date' => $key->production_date,
                    'expired_at' => $key->expired_at,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $db->table('item_move')->insert($list);

            }
            $db->table('item_stock_tag')->where('task_no', $yiku_no)->delete();
            $db->table('item_stock')->whereIn('id', $tag)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $this->sendData(200, '移动完成');

    }
    // 获取未处理的移库单列表
    public function getYikuNos(Request $request)
    {
        $db = DB::connection('labelDB');
        $list = $db->table('item_stock_tag')
        ->select('support_no','task_no','is_whole')
        ->groupBy('support_no','task_no','is_whole')->get();
        return $this->sendData(200, '', $list);
    }
}
