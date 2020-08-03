<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\LabelStock;
use Modules\Label\Models\ItemStock;
use Modules\Label\Models\LabelArrival;
use Modules\Label\Models\Stamp;
use Modules\Label\Models\Location;
use Modules\Label\Models\Outstorage;
use Modules\Label\Models\StampRecord;
use Excel;
use Modules\Label\Models\Invoice;
use Modules\Label\Models\Item;
use PHPExcel_Worksheet_Drawing;

class StampController extends BaseController
{
    // 分页获取盖章单列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('task_no')) $where[] = ['task_no', '=', $request->get('task_no')];
        $tasks = Stamp::where($where)
            ->select([
                'task_no', 'plan_man_power', 'act_man_power', 'starttime', 'endtime', 'status', 'user_id',
                DB::raw('count(id) as item_type'), DB::raw('sum(num) as num_total'), DB::raw('sum(finish_num) as finish_num_total'),
                DB::raw('sum(abandoned_num) as abandoned_num_total'), DB::raw('sum(left_num) as left_num_total'), DB::raw('max(updated_at) as updated_at')
            ])
            ->with('user')
            ->groupBy(['task_no', 'plan_man_power', 'act_man_power', 'starttime', 'endtime', 'status', 'user_id'])
            ->orderBy($sort, 'desc')
            ->paginate($limit);

        return $this->sendData(200, '', $tasks);
    }

    // 分页获取可查询列表
    public function getList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [
            ['status', '=', 0],
            ['expired_at', '=', ''],
        ];

        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        // if ($request->get('mark')) $where[] = ['mark', '=', $request->get('mark')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('label_name')) $where[] = ['label_name', 'like', '%' . $request->get('label_name') . '%'];
        $stocks = DB::connection('labelDB')
            ->table('label_stock')
            ->where($where)
            ->select(['invoice_no', 'item_no', 'label_name', DB::raw('sum(num) as num'), DB::raw('max(updated_at) as updated_at')])
            ->whereExists(function ($q) {
                $q->select('id')->from('item_stock')
                    ->whereRaw('label_stock.item_no=item_stock.item_no and label_stock.invoice_no=item_stock.invoice_no 
            and item_stock.status=2 and item_stock.expired_at <> ""');
            })
            ->groupBy(['invoice_no', 'item_no', 'label_name'])
            ->orderBy($sort, 'desc')
            ->paginate($limit);
        $arr = [];
        // num可用数量,confirm_num确认数量
        foreach ($stocks as $stock) {
            $labelArrival = LabelArrival::where(['invoice_no' => $stock->invoice_no, 'item_no' => $stock->item_no])->first();
            if (!$labelArrival) return $this->sendData(402, $stock->invoice_no . '商品' . $stock->item_no . '标签到货明细数据为空');
            $stock->confirm_num = $labelArrival->confirm_num;
            // 统计已贴标数量
            $search = [
                ['status', '>', 1],
                ['item_no', '=', $stock->item_no],
                ['invoice_no', '=', $stock->invoice_no],
                ['expired_at', '<>', ''],
            ];
            $w = [];
             if ($request->get('expired_at')) $w[] = ['expired_at', '=', $request->get('expired_at')];
             if ($request->get('mark')) $w[] = ['box_mark', '=', $request->get('mark')];
             if ($request->get('mark')) $w[] = ['case_mark', '=', $request->get('mark')];
             if ($request->get('mark')) $w[] = ['box_mark', '=', $request->get('mark')];
            $items = ItemStock::where($search)->where($w)->get();

            // 统计按照有效期，分别统计未贴标和已贴标数量
            $children = [];
            foreach ($items as $item) {
                $key = $this->getMark($item->branch_mark, $item->case_mark, $item->box_mark);
                if (isset($children[$key])) {
                    $children[$key]['item_num'] += $item->num;
                } else {
                    $children[$key] = [
                        'expired_at' => $item->expired_at,
                        'mark' => $key,
                        'item_num' => $item->num,
                        'stamped_num' => 0
                    ];
                }
                if ($item->status == 3) {
                    $children[$key]['stamped_num'] += $item->num;
                }
            }
            // 统计已出库的数量
            $outstorages = Outstorage::where(['invoice_no' => $stock->invoice_no, 'item_no' => $stock->item_no])->get();
            foreach ($outstorages as $outstorage) {
                $key = $this->getMark($outstorage->branch_mark, $outstorage->case_mark, $outstorage->box_mark);
                if (isset($children[$key])) {
                    $children[$key]['item_num'] += $outstorage->num;
                    $children[$key]['stamped_num'] += $outstorage->num;
                } else {
                    $children[$key] = [
                        'expired_at' => $outstorage->expired_at,
                        'mark' => $key,
                        'item_num' => $outstorage->num,
                        'stamped_num' => $outstorage->num
                    ];
                }
            }
            foreach ($children as $key) {
                $arr[] = [
                    'expired_at' => $key['expired_at'],
                    'mark' => $key['mark'],
                    'item_num' => $key['item_num'],
                    'stamped_num' => $key['stamped_num'],
                    'invoice_no' => $stock->invoice_no,
                    'item_no' => $stock->item_no,
                    'label_name' => $stock->label_name,
                    'updated_at' => $stock->updated_at,
                    'confirm_num' => $stock->confirm_num,
                    'num' => $stock->num,
                ];
            }
        }
        return $this->sendData(200, '', $arr);
    }

    public function getLists(Request $request){
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        $status = '9';
        if($request->get('status') == '已创建') $status = '0';
        if($request->get('status') == '已领料') $status = '1';
        if($request->get('status') == '已完成') $status = '2';
        if($request->get('status') == '异常终止') $status = '3';
        if ($request->get('task_no')) $where[] = ['task_no', '=', $request->get('task_no')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('label_name')) $where[] = ['label_name', '=', $request->get('label_name')];
        if ($request->get('mark')) $where[] = ['mark', '=', $request->get('mark')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        if ($request->get('status')) $where[] = ['status', '=', $status];
        if ($request->get('endtime') && $request->get('endtime')[0] && $request->get('endtime')[0] != 'null') {
        $starttime = date('Y-m-d H:i:s', strtotime($request->get('endtime')[0]));
        $endtime = date('Y-m-d H:i:s', strtotime($request->get('endtime')[1]));
        $where[] = ['endtime', '>=', $starttime];
        $where[] = ['endtime', '<=', $endtime];
        }
        $tasks = Stamp::where($where)->with('user')
        ->orderBy($sort, 'desc')
        ->paginate($limit);
        return $this->sendData(200, '', $tasks);
    }

    // 创建查询单
    public function create(Request $request)
    {
        $items = $request->get('items');
        $plan_man_power = (int) $request->get('plan_man_power');
        $starttime = $request->get('starttime');
        if (empty($items) || !is_array($items)) return $this->sendData(402, '提交的数据不能为空');
        if ($plan_man_power <= 0) return $this->sendData(402, '计划人力必须大于0');
        if (!$starttime || !strtotime($starttime)) return $this->sendData(402, '计划开始时间不能为空');
        $starttime = date('Y-m-d H:i:s', strtotime($starttime));
        $res = $this->organizeExpData($items);
        if (!empty($res['code'])) return $this->sendData(402, $res['message']);
        $task_no = 'STAMP' . date('YmdHis') . rand(100, 999);
        try {
            DB::connection('labelDB')->beginTransaction();
            // 插入stamp
            foreach ($res as $item) {
                $insert = [
                    'task_no' => $task_no,
                    'item_no' => $item['item_no'],
                    'invoice_no' => $item['invoice_no'],
                    'num' => $item['total'],
                    'user_id' => $this->user->id,
                    'plan_man_power' => $plan_man_power,
                    'starttime' => $starttime,
                    'label_name' => $item['label_name'],
                    'expired_at' => $item['expired_at'],
                    'mark' => $item['mark'],
                ];
                Stamp::create($insert);
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_STAMP, '盖章单号' . $task_no . '创建');

        return $this->sendData();
    }

    // 根据有效期组织一下提交的数组
    private function organizeExpData($items)
    {
        $data = [];
        foreach ($items as $item) {
            if (empty($item['item_no']) || empty($item['invoice_no'])) {
                return ['code' => 1, 'message' => '发票号或商品代码不能为空'];
            }
            if (empty($item['expired_at'])) {
                return ['code' => 1, 'message' => '有效期不能为空'];
            }
            if (empty($item['mark'])) {
                return ['code' => 1, 'message' => '制造记号不能为空'];
            }
            if (empty($item['label_name'])) {
                return ['code' => 1, 'message' => '标签名称不能为空'];
            }
            if (empty($item['num']) || $item['num'] <= 0) {
                return ['code' => 1, 'message' => '数量必须大于0'];
            }
            $key = $item['invoice_no'] . '_' . $item['item_no'] . '_' . $item['mark'];
            if (empty($data[$key])) {
                $data[$key] = [
                    'total' => $item['num'],
                    'invoice_no' => $item['invoice_no'],
                    'item_no' => $item['item_no'],
                    'mark' => $item['mark'],
                    'expired_at' => $item['expired_at'],
                    'label_name' => $item['label_name'],
                    'data' => [$item],
                ];
            } else {
                $data[$key]['total'] += $item['num'];
                $data[$key]['data'][] = $item;
            }
        }

        return $data;
    }

    // 根据任务编号获取任务
    public function getByNo(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no || !$stamps = Stamp::where('task_no', $task_no)->get()) {
            return $this->sendData(402, '单号错误');
        }
        foreach ($stamps as $stamp) {
            // 获取任务完成信息
            $search = [
                'task_no' => $stamp->task_no,
                'invoice_no' => $stamp->invoice_no,
                'mark' => $stamp->mark,
                'item_no' => $stamp->item_no,
            ];
            $stamp->records = StampRecord::where($search)->get();
            // 获取需要补充的贴标数量
            $search = [
                'invoice_no' => $stamp->invoice_no,
                'item_no' => $stamp->item_no,
                'mark' => $stamp->mark,
                'status' => 1,
                'location_no' => '暂存区'
            ];
            $stamp->sticked = LabelStock::where($search)->first();
        }

        return $this->sendData(200, '', $stamps);
    }

    // 结束盖章单
    public function complete(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');
        $stamps = Stamp::where(['task_no' => $task_no, 'status' => 1])->get();
        if (!$stamps) return $this->sendData(402, '该单不存在或已结束');
        $act_man_power = $request->get('act_man_power');
        if ($act_man_power <= 0) return $this->sendData(402, '实际使用人力必须大于0');
        $endtime = $request->get('endtime');
        if (!$endtime || !strtotime($endtime)) return $this->sendData(402, '计划结束时间不能为空');
        $endtime = date('Y-m-d H:i:s', strtotime($endtime));
        $items = $request->get('items');
        if (empty($items) || !is_array($items)) return $this->sendData(402, '提交的数据不能为空');
        $all = [];

        foreach ($items as $item){
            for($i = 1;$i<=3;$i++){
                if($i = 1){
                    if($item['num'] == null){
                        $item['location_no'] = 0;
                        $item['num'] = 0;
                    }
                    $all[] = [
                        "invoice_no" => $item['invoice_no'],
                        "item_no" => $item['item_no'],
                        "location_no" => $item['location_no'],
                        "num" => $item['num'],
                        "label_name" => $item['label_name'],
                        "mark" => $item['mark'],
                        "expired_at" => $item['expired_at'],
                    ];
                }
                if($i = 2){
                    if($item['num1'] == null){
                        $item['location_no1'] = 0;
                        $item['num1'] = 0;
                    }
                    $all[] = [
                        "invoice_no" => $item['invoice_no'],
                        "item_no" => $item['item_no'],
                        "location_no" => $item['location_no1'],
                        "num" => $item['num1'],
                        "label_name" => $item['label_name'],
                        "mark" => $item['mark'],
                        "expired_at" => $item['expired_at'],
                    ];
                }
                if($i = 3){
                    if($item['num2'] == null){
                        $item['location_no2'] = 0;
                        $item['num2'] = 0;
                    }
                    $all[] = [
                        "invoice_no" => $item['invoice_no'],
                        "item_no" => $item['item_no'],
                        "location_no" => $item['location_no2'],
                        "num" => $item['num2'],
                        "label_name" => $item['label_name'],
                        "mark" => $item['mark'],
                        "expired_at" => $item['expired_at'],
                    ];
                }
            }
        }
        foreach($all as $list=>$value){
            if($value['num'] == 0){
                unset($all[$list]);
                continue;
            }
        }
        $res = $this->getOrganizedData($all, $stamps);
        if (!empty($res['code'])) return $this->sendData(402, $res['message']);
        $res = $res['data'];
        $labelData = [];
        foreach ($stamps as $stamp) {
            $key = $stamp->invoice_no . '_' . $stamp->item_no;
            if (!isset($labelData[$key])) {
                $s = ['invoice_no' => $stamp->invoice_no, 'item_no' => $stamp->item_no];
                $arrival = LabelArrival::where($s)->first();
                $labelData[$key] = [
                    'invoice_no' => $arrival->invoice_no,
                    'item_no' => $arrival->item_no,
                    'arrival' => $arrival,
                    'origin_num' => 0,
                    'submit_num' => 0
                ];
            }
            $labelData[$key]['origin_num'] += $stamp->num;
        }

        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($stamps as $stamp) {
                $labelDataIndex = $stamp->invoice_no . '_' . $stamp->item_no;
                $resIndex = $labelDataIndex . '_' . $stamp->mark;
                // 先插入已盖章和报废的到库存和盖章记录表
                foreach (array_merge($res[$resIndex]['finished'], $res[$resIndex]['abandoned'], $res[$resIndex]['left'], $res[$resIndex]['sticked']) as $item) {
                    $labelData[$labelDataIndex]['submit_num'] += $item['num'];
                    $insert = [
                        'num' => $item['num'],
                        'invoice_no' => $item['invoice_no'],
                        'item_no' => $item['item_no'],
                        'location_no' => $item['location_no'],
                        'label_name' => $item['label_name'],
                        'mark' => $item['status'] ? $item['mark'] : '',
                        'expired_at' => $item['status'] ? $item['expired_at'] : '',
                        'status' => $item['status']
                    ];
                    $this->labelIn($insert);
                    $insert['task_no'] = $task_no;
                    $insert['type'] = 0;
                    if (!$item['status']) {
                        $insert['expired_at'] = $item['expired_at'];
                        $insert['mark'] = $item['mark'];
                    }
                    StampRecord::create($insert);
                }
                // // 补充贴标拿走的库存
                // if (!empty($res[$resIndex]['sticked'])) {
                //     $search = [
                //         'status' => 1,
                //         'location_no' => '暂存区',
                //         'invoice_no' => $stamp->invoice_no,
                //         'item_no' => $stamp->item_no,
                //         'mark' => $stamp->mark,
                //     ];
                //     $stock = LabelStock::where($search)->first();
                //     dd($res[$resIndex]);
                //     if (!$stock) throw new \Exception($stamp->item_no . '无需补充的标签，请刷新页面重试');
                //     $stock->num += $res[$resIndex]['sticked_total'];
                //     if ($stock->num > 0) throw new \Exception($stamp->item_no . '补充的标签超过限制，请刷新页面重试');
                //     if ($stock->num == 0) {
                //         $stock->deldelete();
                //     } else {
                //         $stock->save();
                //     }
                // }
                // 更新盖章总表
                $stamp->act_man_power = $act_man_power;
                $stamp->endtime = $endtime;
                $stamp->finish_num = $res[$resIndex]['finished_total'] + $res[$resIndex]['sticked_total'];
                $stamp->abandoned_num = $res[$resIndex]['abandoned_total'];
                $stamp->left_num = $res[$resIndex]['left_total'];
                $stamp->status = 2;
                $stamp->save();
            }

            // 更新标签到货明细表、库存表和盖章记录表
            foreach ($res as $v) {
                $search = [
                    'invoice_no' => $v['invoice_no'],
                    'item_no' => $v['item_no'],
                    'status' => 0,
                    'type' => 1
                ];
                $stocks = LabelStock::where($search)->get();
                $total = $v['sticked_total'];
                foreach ($stocks as $stock) {
                    if ($total == 0) break;
                    $insertRecord = [
                        'invoice_no' => $stock->invoice_no,
                        'item_no' => $stock->item_no,
                        'location_no' => $stock->location_no,
                        'label_name' => $stock->label_name,
                        'expired_at' => '',
                        'mark' => '',
                        'status' => 0,
                        'type' => 1,
                        'task_no' => $task_no,
                    ];
                    if ($stock->num <= $total) {
                        $insertRecord['num'] = $stock->num;
                        StampRecord::create($insertRecord);
                        if ($stock->frozen_num == 0) {
                            $stock->delete();
                        } else {
                            $stock->num = 0;
                            $stock->save();
                        }
                        $total -= $stock->num;
                    } else {
                        $insertRecord['num'] = $total;
                        StampRecord::create($insertRecord);
                        $stock->num -= $total;
                        $stock->save();
                        $total = 0;
                    }
                }
                if ($total > 0) throw new \Exception($v['item_no'] . '未盖章标签库存不足，请确认未盖章标签数量');
                $arrival->confirm_num = $arrival->confirm_num + $v['sticked_total'];
                $arrival->save();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_STAMP, '单号' . $task_no . '完成盖章');

        return $this->sendData();
    }

    // 获取组织后的数据
    private function getOrganizedData($items, $stamps)
    {
        $data = [];
        // 获取未盖章，已盖章和盖章报废的库位
        $locations = Location::where('type', 1)->whereIn('label_type', [0, 1, 2])->get();
        $locationLeft = $locationFinished = $locationAbandoned = [];
        foreach ($locations as $location) {
            if ($location->label_type == 1) {
                $locationFinished[] = $location->location_no;
            } elseif ($location->label_type == 2) {
                $locationAbandoned[] = $location->location_no;
            } else {
                $locationLeft[] = $location->location_no;
            }
        }
        // 获取该单中所有的商品种类
        $indexList = [];
        foreach ($stamps as $stamp) {
            $key = $stamp->invoice_no . '_' . $stamp->item_no . '_' . $stamp->mark;
            if (!in_array($key, $indexList)) $indexList[] = $key;
        }
        // 开始验证
        foreach ($items as $item) {
            if (empty($item['item_no'])) return ['code' => 1, 'message' => '商品代码不能为空'];
            if (empty($item['invoice_no'])) return ['code' => 1, 'message' => $item['item_no'] . '发票号不能为空'];
            if (empty($item['location_no'])) return ['code' => 1, 'message' => $item['item_no'] . '库位号不能为空'];
            if (empty($item['label_name'])) return ['code' => 1, 'message' => $item['item_no'] . '标签名称不能为空'];
            if (empty($item['mark'])) return ['code' => 1, 'message' => $item['item_no'] . '制造记号不能为空'];
            if (empty($item['expired_at'])) return ['code' => 1, 'message' => $item['item_no'] . '有效期不能为空'];
            if (empty($item['num']) || $item['num'] <= 0) {
                return ['code' => 1, 'message' => $item['item_no'] . '数量必须大于0'];
            }
            $index = $item['invoice_no'] . '_' . $item['item_no'] . '_' . $item['mark'];
            if (!in_array($index, $indexList)) {
                return ['code' => 1, 'message' => $item['item_no'] . '制造记号' . $item['mark'] . '不在该计划单中'];
            }
            if (!isset($data[$index])) {
                $data[$index] = [
                    'finished_total' => 0,
                    'abandoned_total' => 0,
                    'sticked_total' => 0,
                    'left_total' => 0,
                    'invoice_no' => $item['invoice_no'],
                    'item_no' => $item['item_no'],
                    'mark' => $item['mark'],
                    'label_name' => $item['label_name'],
                    'finished' => [],
                    'abandoned' => [],
                    'left' => [],
                    'sticked' => []
                ];
            }
            if (in_array($item['location_no'], $locationFinished)) {
                // 已盖章的
                $item['status'] = 1;
                $data[$index]['finished_total'] += $item['num'];
                $data[$index]['finished'][] = $item;
            } elseif (in_array($item['location_no'], $locationAbandoned)) {
                // 报废的
                $item['status'] = 2;
                $data[$index]['abandoned_total'] += $item['num'];
                $data[$index]['abandoned'][] = $item;
            } elseif (in_array($item['location_no'], $locationLeft)) {
                // 剩余的
                $item['status'] = 0;
                $data[$index]['left_total'] += $item['num'];
                $data[$index]['left'][] = $item;
            } elseif ($item['location_no'] == '暂存区') {
                $item['status'] = 1;
                $data[$index]['sticked_total'] += $item['num'];
                $data[$index]['sticked'][] = $item;
            } else {
                return ['code' => 1, 'message' => $item['item_no'] . '库位错误，必须为暂存区,未盖章，已盖章或盖章报废的库位'];
            }
        }
        if (count($data) != count($indexList)) return ['code' => 1, 'message' => '提交的商品种类不符'];

        return ['code' => 0, 'data' => $data];
    }

    // 终止计划单
    public function terminate(Request $request)
    {
        $task_no = $request->get('task_no');
        $status = $request->get('status');

        if (!$task_no) return $this->sendData(402, '单号不能为空');
        $stamps = Stamp::where(['task_no' => $task_no, 'status' => 0])->get();
        if (!$stamps) return $this->sendData(402, '该单不存在或已结束');
        if ($status == '已领料') {
            $stamps = StampRecord::where('task_no', $task_no)->get();
            foreach ($stamps as $stamp) {
                $where = [
                    'type' => 1,
                    'invoice_no' => $stamp->invoice_no,
                    'item_no' => $stamp->item_no,
                    'location_no' => $stamp->location_no
                ];
                $label = LabelStock::where($where)->first();
                $label->num -= $stamp->num;
                if ($label->num == 0) {
                    $label->delete();
                }
                $data = [
                    'invoice_no' => $stamp->invoice_no,
                    'num' => $stamp->num,
                    'location_no' => $stamp->location_no,
                    'status' => $stamp->status,
                    'type' => 0,
                    'expired_at' => $stamp->expired_at,
                    'mark' => $stamp->mark,
                    'item_no' => $stamp->item_no,
                    'label_name' => $stamp->label_name,
                    'frozen_num' => 0,
                ];
                $this->labelIn($data);
            }
        }
        Stamp::where('task_no', $task_no)->update(['status' => 3]);
        $this->log(self::LOG_STAMP, '单号' . $task_no . '异常终止');

        return $this->sendData();
    }

    // 导出
    public function export(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return '发票号不能为空';

        $stamps = Stamp::where('task_no', $task_no)->get();
        if (count($stamps) == 0) return '单号错误';
        $error = '';
        foreach ($stamps as $stamp) {
            $item = Item::where('item_no', $stamp->item_no)->first();
            if (!$item) {
                $error .= $stamp->item_no . "无商品基础信息，无法导出<br>";
            } else {
                $stamp->item = $item;
            }
        }
        if ($error) return $error;
        $fileName = '加工记录表';
        return Excel::create($fileName, function ($excel) use ($stamps) {
            $excel->setTitle('盖章导出');
            foreach ($stamps as $stamp) {
                $sheetName = $stamp->item_no . '_' . $stamp->mark . '_' . str_replace('/', '-', $stamp->invoice_no);
                $excel->sheet($sheetName, function ($sheet) use ($stamp) {
                    $item = $stamp->item;
                    // 获取制造记号
                    $data = [
                        ['加工记录表'],
                        ['产品基本信息', '货品编码', '中文名', '', '制造记号', '效期', '数量/pcs', '箱入数', "盒入数", '制造记号', '效期', '数量/pcs'],
                        [$stamp->invoice_no, $stamp->item_no, $stamp->label_name, '', $stamp->mark, date('Y年m月', strtotime($stamp->expired_at)), $stamp->num],
                        ['敲章信息', '请在背面贴上一张相关标签作为样品（敲上效期）'],
                        ['敲章人/工号', '时间', '效期', '领标数量', '敲章数量', '报废数量', '良品数', '抽检人', '时间', '检查数量', '良品', '次品'],
                        [],
                        [],
                        [],
                        [],
                        [],
                        [],
                        ['', '', '', '', '样品数', '', '', '良品总数', '', '敲章报废总数'],
                        ['贴标信息', '请在背面贴上一张中盒贴，外箱贴'],
                        ['领标人', '领取时间', '中盒效期', '中盒标签数', '箱数', '报废数量', '领标人', '领取时间', '中盒效期', '箱数', '中盒标签数', '报废数量'],
                        [],
                        [],
                        [],
                        [],
                        [],
                        [],
                        [],
                        [],
                        [],
                        ['贴标报废总数：', '', '复领标签数：', '', '复敲报废数：', '', '', '复领标签样品数：', '', '', '组长确认：'],
                        ['贴标注意事项：' . $item->note],
                    ];
                    $sheet->fromArray($data, null, 'A1', false, false);
                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  '微软雅黑',
                            'size'      =>  11,
                            'bold'      =>  false
                        ),
                    ));
                    $sheet->row(1, function ($row) {
                        $row->setFontWeight('bold');
                        $row->setFontSize('12');
                        $row->setFontFamily('微软雅黑');
                    });
                    // 设置样式
                    $style_array = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
                    $sheet->getStyle('A1:L25')->applyFromArray($style_array);
                    $sheet->setHeight('1', 40);
                    $sheet->setHeight('25', 40);
                    $sheet->setWidth('A', 11);
                    $sheet->setWidth('B', 10);
                    $sheet->setWidth('C', 15);
                    $sheet->setWidth('D', 15);
                    $sheet->setWidth('E', 10);
                    $sheet->setWidth('F', 8);
                    $sheet->setWidth('G', 8);
                    $sheet->setWidth('H', 8);
                    $sheet->setWidth('I', 10);
                    $sheet->setWidth('J', 10);
                    $sheet->setWidth('K', 10);
                    $sheet->setWidth('L', 10);
                    // 合并单元格
                    $sheet->mergeCells('A1:L1');
                    $sheet->mergeCells('C2:D2');
                    $sheet->mergeCells('C3:D3');
                    $sheet->mergeCells('B4:L4');
                    $sheet->mergeCells('E12:F12');
                    $sheet->mergeCells('J12:K12');
                    $sheet->mergeCells('B13:L13');
                    $sheet->mergeCells('A24:B24');
                    $sheet->mergeCells('C24:D24');
                    $sheet->mergeCells('E24:F24');
                    $sheet->mergeCells('H24:J24');
                    $sheet->mergeCells('K24:L24');
                    $sheet->mergeCells('A25:L25');
                    // 样式设置
                    $sheet->cells('A1:L23', function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                    $sheet->cells('A21:L25', function ($cells) {
                        $cells->setAlignment('left');
                        $cells->setValignment('left');
                    });
                    // 插入logo
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('logo');
                    $objDrawing->setDescription('logo');
                    $objDrawing->setPath(public_path('image') . DIRECTORY_SEPARATOR . 'logo.png');
                    $objDrawing->setHeight(50);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($sheet);
                });
            }
        })->export('xls');
    }
    public function exportBooks(Request $request){
        $task_no = $request->get('task_no');
        $db = DB::connection('labelDB');
        if (!$task_no) return $this->sendData(402, '发票号不能为空');
        $stamps = Stamp::where('task_no', $task_no)->get();
        if (count($stamps) == 0) return $this->sendData(402, '单号错误');
        $image = [];
        foreach($stamps as $list){
            $new = Invoice::where('item_no',$list->item_no)->where('invoice_no',$list->invoice_no)->first();
            if(!$new){
                return $this->sendData(402, '数据错误');
            }
            $img = Item::where('material_code',$new->material_code)->first();
            if($img){
                if(!empty($img->img)){
                        $image[] = $img->img;
                }
            }
        }
        $result_01 = array_flip($image);
        $result  = array_keys($result_01);
        $name = date('YmdHis') . rand('1', '999');
        return Excel::create($name, function ($excel) use ($result) {
            $excel->sheet('score', function ($sheet) use ($result) {
                $sum = 1;
                foreach($result as $key =>$val){
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path(). '/' .'uploads'. '/' . $val);
                    $objDrawing->setCoordinates('A'.$sum);
                    $objDrawing->setHeight(400);
                    $objDrawing->setWidth(300);
                    $objDrawing->setOffsetX(100); //写入图片在指定格中的X坐标值
                    $objDrawing->setOffsetY(-25); //写入图片在指定格中的Y坐标值
                    $objDrawing->setRotation(1); //设置旋转角度
                    $objDrawing->getShadow()->setVisible(true); //
                    $objDrawing->getShadow()->setDirection(50); //
                    $objDrawing->setWorksheet($sheet);
                    $sum += 20;
                }  
            });
        })->export('xls');
    }

    public function exportPicking(Request $request)
    {
        $task_no = $request->get('task_no');
        $db = DB::connection('labelDB');
        if (!$task_no) return $this->sendData(402, '发票号不能为空');
        $stamps = Stamp::where('task_no', $task_no)->get();
        if (count($stamps) == 0) return $this->sendData(402, '单号错误');
        try {
            // 开启事务
            DB::beginTransaction();
            $stamp = StampRecord::where('task_no', $task_no)->where('type', 1)->get();
            $status = $request->get('status');

            if (count($stamp) == 0) {
                foreach ($stamps as $key) {
                    $where = [
                        'item_no' => $key->item_no,
                        'invoice_no' => $key->invoice_no,
                        'type' => 0,
                    ];
                    $sum = LabelStock::where($where)->sum('num');
                    if ($sum < $key->num) {
                        return '可用标签库存不足';
                    } else {
                        if ($status == '已创建') {
                            Stamp::where('task_no', $task_no)->update(['status' => 1]);
                        };
                        $res = LabelStock::where($where)->orderBy('location_no','ASC')->get();
                        foreach ($res as $val) {
                            if ($key->num < $val->num) {
                                $val->num -= $key->num;
                                $val->save();
                                $data = [
                                    'invoice_no' => $val->invoice_no,
                                    'num' => $key->num,
                                    'location_no' => $val->location_no,
                                    'status' => $val->status,
                                    'type' => 1,
                                    'expired_at' => $val->expired_at,
                                    'mark' => $val->mark,
                                    'item_no' => $val->item_no,
                                    'label_name' => $val->label_name,
                                    'frozen_num' => 0,
                                ];
                                $this->labelIn($data);
                                $arr = [
                                    'task_no' => $task_no,
                                    'mark' => $val->mark,
                                    'num' => $key->num,
                                    'invoice_no' => $val->invoice_no,
                                    'item_no' => $val->item_no,
                                    'label_name' => $val->label_name,
                                    'location_no' => $val->location_no,
                                    'expired_at' => $val->expired_at,
                                    'status' => $val->status,
                                    'type' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $db->table('stamp_records')->insert($arr);
                                $key->num = 0;
                                break;
                            } else if ($key->num == $val->num) {
                                $data = [
                                    'invoice_no' => $val->invoice_no,
                                    'num' => $key->num,
                                    'location_no' => $val->location_no,
                                    'status' => $val->status,
                                    'type' => 1,
                                    'expired_at' => $val->expired_at,
                                    'mark' => $val->mark,
                                    'item_no' => $val->item_no,
                                    'label_name' => $val->label_name,
                                    'frozen_num' => 0,
                                ];
                                $this->labelIn($data);
                                $arr = [
                                    'task_no' => $task_no,
                                    'mark' => $val->mark,
                                    'num' => $key->num,
                                    'invoice_no' => $val->invoice_no,
                                    'item_no' => $val->item_no,
                                    'label_name' => $val->label_name,
                                    'location_no' => $val->location_no,
                                    'expired_at' => $val->expired_at,
                                    'status' => $val->status,
                                    'type' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $db->table('stamp_records')->insert($arr);
                                $val->delete();
                                $key->num = 0;
                                break;
                            } else {
                                $data = [
                                    'invoice_no' => $val->invoice_no,
                                    'num' => $val->num,
                                    'location_no' => $val->location_no,
                                    'status' => $val->status,
                                    'type' => 1,
                                    'expired_at' => $val->expired_at,
                                    'mark' => $val->mark,
                                    'item_no' => $val->item_no,
                                    'label_name' => $val->label_name,
                                    'frozen_num' => 0,
                                ];
                                $this->labelIn($data);
                                $arr = [
                                    'task_no' => $task_no,
                                    'mark' => $val->mark,
                                    'num' => $val->num,
                                    'invoice_no' => $val->invoice_no,
                                    'item_no' => $val->item_no,
                                    'label_name' => $val->label_name,
                                    'location_no' => $val->location_no,
                                    'expired_at' => $val->expired_at,
                                    'status' => $val->status,
                                    'type' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $db->table('stamp_records')->insert($arr);
                                $key->num -= $val->num;
                            }
                        }
                    }
                }
                $stamp = StampRecord::where('task_no', $task_no)->where('type', 1)->get();
                $this->exports($stamp);
            } else {
                if ($status == '已创建') {
                    Stamp::where('task_no', $task_no)->update(['status' => 1]);
                };
                $this->exports($stamp);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function exports($data)
    {
        $pre = $prf = [];
        $pre_key = '';
        foreach ($data as $k => $info) {
            $key = $info->invoice_no . '-' . $info->item_no . '-' . $info->location_no;
            if ($k === 0) {
                $pre[$key] = $info;
                $pre_key = $key;
                continue;
            }
            if (array_key_exists($key, $pre)) {
                $pre[$key]->num += $info->num;
            } else {
                $prf[] = $pre[$pre_key];
                $pre = [];
                $pre_key = $key;
                $pre[$key] = $info;
            }
        }
        // 最后一条特殊处理
        $prf[] = $pre[$pre_key];
        $res = [['任务编号:' . $data[0]['task_no']], ['发票号', '产品代码', '中文名称', '领料数', '库位']];
        foreach ($prf as $stamp) {
            $res[] = [
                $stamp->invoice_no,
                $stamp->item_no,
                $stamp->label_name,
                $stamp->num,
                $stamp->location_no,
            ];
        }
        $name = date('YmdHis') . rand('1', '999');
        return Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->rows($res);
            });
        })->export('xls');
    }
}
