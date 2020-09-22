<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\LabelStock;
use Modules\Label\Models\ItemStock;
use Modules\Label\Models\Invoice;
use Modules\Label\Models\Stick;
use Modules\Label\Models\StickItemRecord;
use Modules\Label\Models\StickLabelRecord;
use Modules\Label\Models\StickItemRecover;
use Modules\Label\Models\StickLabelRecover;
use Modules\Label\Models\StickPick;
use Excel;
use Modules\Label\Models\LabelArrival;
use PHPExcel_Worksheet_Drawing;
use Modules\Label\Models\Item;

class StickController extends BaseController
{
    // 分页获取贴标单列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'task_no';
        $where = [];
        if ($request->get('task_no')) $where[] = ['task_no', '=', $request->get('task_no')];
        $tasks = Stick::where($where)
            ->select([
                'task_no', 'plan_man_power', 'act_man_power', 'starttime', 'endtime', DB::raw('max(status) as status'), 'user_id',
                DB::raw('count(id) as item_type'), DB::raw('sum(num) as num_total'), DB::raw('sum(finish_num) as finish_num_total'),
                DB::raw('sum(abandoned_num) as abandoned_num_total'), DB::raw('sum(pick_num) as pick_num_total'), DB::raw('max(updated_at) as updated_at'),
                DB::raw('sum(item_left_num) as item_left_num_total'), DB::raw('sum(label_left_num) as label_left_num_total')
            ])
            ->with('user')
            ->groupBy(['task_no', 'plan_man_power', 'act_man_power', 'starttime', 'endtime', 'user_id'])
            ->orderBy($sort, 'desc')
            ->paginate($limit);
        return $this->sendData(200, '', $tasks);
    }

    // 根据任务编号获取任务
    public function getByNo(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no || !$sticks = Stick::where('task_no', $task_no)->with('picks')->get()) {
            return $this->sendData(402, '单号错误');
        }
        foreach ($sticks as $stick) {
            // 获取任务完成信息
            $search = [
                'task_no' => $stick->task_no,
                'invoice_no' => $stick->invoice_no,
                'box_mark' => $stick->box_mark,
                'case_mark' => $stick->case_mark,
                'branch_mark' => $stick->branch_mark,
                'item_no' => $stick->item_no,
            ];
            $stick->itemRecords = StickItemRecord::where($search)->get();
            $search = [
                'task_no' => $stick->task_no,
                'invoice_no' => $stick->invoice_no,
                'mark' => $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark),
                'item_no' => $stick->item_no,
            ];
            $stick->labelRecords = StickLabelRecord::where($search)->get();
        }

        return $this->sendData(200, '', $sticks);
    }

    // 分页获取可贴标商品列表
    public function getList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [
            ['s.status', '=', 2],
            ['s.state', '=', 0],
            ['i.is_stick_valid', '=', 0],
        ];
        if ($request->get('invoice_no')) $where[] = ['s.invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('item_no')) $where[] = ['s.item_no', '=', $request->get('item_no')];
        if ($request->get('material_code')) $where[] = ['s.material_code', '=', $request->get('material_code')];
        if ($request->get('box_mark')) $where[] = ['s.box_mark', '=', $request->get('box_mark')];
        if ($request->get('case_mark')) $where[] = ['s.case_mark', '=', $request->get('case_mark')];
        if ($request->get('branch_mark')) $where[] = ['s.branch_mark', '=', $request->get('branch_mark')];
        if ($request->get('expired_at')) $where[] = ['s.expired_at', '=', $request->get('expired_at')];
        if ($request->get('item_name')) $where[] = ['s.item_name', 'like', '%' . $request->get('item_name') . '%'];
        $stocks = DB::connection('labelDB')
            ->table('item_stock as s')
            ->leftJoin('items as i', 's.material_code', '=', 'i.material_code')
            ->where($where)
            ->select([
                's.invoice_no', 's.item_no', 's.material_code', 's.branch_mark', 's.expired_at', 's.item_name', 's.id',
                's.box_mark', 's.case_mark', 's.valid_month', 's.production_date',
                DB::raw('sum(s.num) as num'), DB::raw('max(s.updated_at) as updated_at')
            ])
            ->whereExists(function ($q) {
                $q->select('id')->from('label_stock as l')
                    ->whereRaw('l.item_no=s.item_no AND l.invoice_no=s.invoice_no AND l.num>0 AND l.status in(0,1)');
            })
            ->groupBy([
                's.invoice_no', 's.item_no', 's.material_code', 's.branch_mark', 's.expired_at', 's.item_name', 's.id',
                's.box_mark', 's.case_mark', 's.valid_month', 's.production_date',
            ])
            ->orderBy($sort, 'desc')
            ->paginate($limit);

        foreach ($stocks as $stock) {
            $mark = $this->getMark($stock->branch_mark, $stock->case_mark, $stock->box_mark);
            $search = [
                'invoice_no' => $stock->invoice_no,
                'item_no' => $stock->item_no,
                'mark' => $mark,
                'type' => 0,
                'status' => 1
            ];
            $stock->label_num = LabelStock::where($search)->sum('num');
        }

        return $this->sendData(200, '', $stocks);
    }
    public function getCartList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [
            ['s.status', '=', 2],
            ['s.state', '=', 3],
            ['i.is_stick_valid', '=', 0],
        ];
        if ($request->get('invoice_no')) $where[] = ['s.invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('item_no')) $where[] = ['s.item_no', '=', $request->get('item_no')];
        $stocks = DB::connection('labelDB')
            ->table('item_stock as s')
            ->leftJoin('items as i', 's.material_code', '=', 'i.material_code')
            ->where($where)
            ->select([
                's.invoice_no', 's.item_no', 's.material_code', 's.branch_mark', 's.expired_at', 's.item_name',
                's.box_mark', 's.case_mark', 's.valid_month', 's.production_date', 's.id',
                DB::raw('sum(s.num) as num'), DB::raw('max(s.updated_at) as updated_at')
            ])
            ->whereExists(function ($q) {
                $q->select('id')->from('label_stock as l')
                    ->whereRaw('l.item_no=s.item_no AND l.invoice_no=s.invoice_no AND l.num>0 AND l.status in(0,1)');
            })
            ->groupBy([
                's.invoice_no', 's.item_no', 's.material_code', 's.branch_mark', 's.expired_at', 's.item_name',
                's.box_mark', 's.case_mark', 's.valid_month', 's.production_date', 's.id',
            ])
            ->orderBy($sort, 'desc')
            ->paginate($limit);

        foreach ($stocks as $stock) {
            $mark = $this->getMark($stock->branch_mark, $stock->case_mark, $stock->box_mark);
            $search = [
                'invoice_no' => $stock->invoice_no,
                'item_no' => $stock->item_no,
                'mark' => $mark,
                'type' => 0,
                'status' => 1
            ];
            $stock->label_num = LabelStock::where($search)->sum('num');
        }

        return $this->sendData(200, '', $stocks);
    }
    public function getLists(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'task_no';
        $status = '9';
        if ($request->get('status') == '未开始') $status = '0';
        if ($request->get('status') == '商品领料') $status = '1';
        if ($request->get('status') == '标签已分配') $status = '2';
        if ($request->get('status') == '标签已领料') $status = '3';
        if ($request->get('status') == '商品已领料+标签已分配') $status = '4';
        if ($request->get('status') == '商品已领料+标签已领料') $status = '5';
        if ($request->get('status') == '已完成') $status = '6';
        if ($request->get('status') == '异常终止') $status = '7';
        $where = [];
        if ($request->get('task_no')) $where[] = ['task_no', '=', $request->get('task_no')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('name')) $where[] = ['name', '=', $request->get('name')];
        if ($request->get('box_mark')) $where[] = ['box_mark', '=', $request->get('box_mark')];
        if ($request->get('branch_mark')) $where[] = ['branch_mark', '=', $request->get('branch_mark')];
        if ($request->get('case_mark')) $where[] = ['case_mark', '=', $request->get('case_mark')];
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        if ($request->get('status')) $where[] = ['status', '=', $status];
        if ($request->get('updated_at') && $request->get('updated_at')[0] && $request->get('updated_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('updated_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('updated_at')[1]));
            $where[] = ['updated_at', '>=', $starttime];
            $where[] = ['updated_at', '<=', $endtime];
        }
        $tasks = Stick::where($where)
            ->with('user')
            ->orderBy($sort, 'desc')
            ->paginate($limit);
        return $this->sendData(200, '', $tasks);
    }

    public function shopping(Request $request)
    {
        $all = $request->all();
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($all as $val) {
                $tasks = ItemStock::where('id', $val['id'])->first();
                if ($val['plan_num'] > $tasks->num) {
                    return $this->sendData(402, '数量错误');
                }
                if ($tasks->num == $val['plan_num']) {
                    $tasks->state = '3';
                    $tasks->save();
                }
                if ($val['plan_num'] < $tasks->num) {
                    $data = [
                        'num' => $val['plan_num'],
                        'invoice_no' => $tasks->invoice_no,
                        'item_no' => $tasks->item_no,
                        'material_code' => $tasks->material_code,
                        'item_name' => $tasks->item_name,
                        'location_no' => $tasks->location_no,
                        'support_no' => $tasks->support_no,
                        'box_mark' => $tasks->box_mark,
                        'case_mark' => $tasks->case_mark,
                        'branch_mark' => $tasks->branch_mark,
                        'valid_month' => $tasks->valid_month,
                        'status' => $tasks->status,
                        'production_date' => $tasks->production_date,
                        'expired_at' => $tasks->expired_at,
                        'state' => '3',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    ItemStock::create($data);
                    $tasks->num = $tasks->num - $val['plan_num'];
                    $tasks->save();
                }
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        return $this->sendData();
    }


    // 创建计划单
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
        $task_no = 'STICK' . date('YmdHis') . rand(100, 999);
        try {
            foreach ($items as $val) {
                $where = [
                    'invoice_no' => $val['invoice_no'],
                    'item_no' => $val['item_no'],
                    'num' => $val['num'],
                    'box_mark' => $val['box_mark'] ? $val['box_mark']:'',
                    'case_mark' => $val['case_mark'] ? $val['case_mark'] :'',
                    'branch_mark' => $val['branch_mark'] ? $val['branch_mark'] :'',
                    'valid_month' => $val['valid_month'] ? $val['valid_month'] :'',
                    'expired_at' => $val['expired_at'] ? $val['expired_at'] :'',
                ];
                $stock = ItemStock::where($where)->first();
                $stock->state = 4;
                $stock->save();
            }
            DB::connection('labelDB')->beginTransaction();
            // 插入stick
            foreach ($res as $item) {
                $insert = [
                    'task_no' => $task_no,
                    'item_no' => $item['item_no'],
                    'invoice_no' => $item['invoice_no'],
                    'num' => $item['total'],
                    'user_id' => $this->user->id,
                    'plan_man_power' => $plan_man_power,
                    'starttime' => $starttime,
                    'name' => $item['name'],
                    'expired_at' => $item['expired_at'] ? $item['expired_at'] : '',
                    'material_code' => $item['material_code'],
                    'production_date' => $item['production_date'] ? $item['production_date'] : '',
                    'box_mark' => $item['box_mark'] ? $item['box_mark'] : '',
                    'case_mark' => $item['case_mark'] ? $item['case_mark'] : '',
                    'branch_mark' => $item['branch_mark'] ? $item['branch_mark'] : '',
                    'valid_month' => $item['valid_month'] > 0 ? $item['valid_month'] : 0,
                ];
                Stick::create($insert);
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_STICK, '贴标单号' . $task_no . '创建');

        return $this->sendData();
    }

    // 根据制造记号组织一下提交的数组
    private function organizeExpData($items)
    {
        $data = [];
        foreach ($items as $item) {
            if (empty($item['item_no']) || empty($item['invoice_no'])) {
                return ['code' => 1, 'message' => '发票号或商品代码不能为空'];
            }
            // if (!isset($item['expired_at'])) {
            //     return ['code' => 1, 'message' => '有效期字段必须存在'];
            // }
            // if (!isset($item['production_date'])) {
            //     return ['code' => 1, 'message' => '生产日期字段必须存在'];
            // }
            // if (!isset($item['box_mark'])) {
            //     return ['code' => 1, 'message' => '箱制造记号字段必须存在'];
            // }
            // if (!isset($item['case_mark'])) {
            //     return ['code' => 1, 'message' => '盒制造记号字段必须存在'];
            // }
            // if (!isset($item['branch_mark'])) {
            //     return ['code' => 1, 'message' => '支制造记号字段必须存在'];
            // }
            // if (!isset($item['valid_month'])) {
            //     return ['code' => 1, 'message' => '有效月份字段必须存在'];
            // }
            if (empty($item['material_code'])) {
                return ['code' => 1, 'message' => '物料编码不能为空'];
            }
            if (empty($item['name'])) {
                return ['code' => 1, 'message' => '名称不能为空'];
            }
            if (empty($item['num']) || $item['num'] <= 0) {
                return ['code' => 1, 'message' => '数量必须大于0'];
            }
            $mark = $this->getMark($item['branch_mark'], $item['case_mark'], $item['box_mark']);
            $key = $item['invoice_no'] . '_' . $item['item_no'] . '_' . $mark;
            if (empty($data[$key])) {
                $data[$key] = [
                    'total' => $item['num'],
                    'invoice_no' => $item['invoice_no'],
                    'item_no' => $item['item_no'],
                    'expired_at' => $item['expired_at'],
                    'branch_mark' => $item['branch_mark'],
                    'case_mark' => $item['case_mark'],
                    'box_mark' => $item['box_mark'],
                    'valid_month' => $item['valid_month'],
                    'material_code' => $item['material_code'],
                    'production_date' => $item['production_date'],
                    'name' => $item['name'],
                    'data' => [$item],
                ];
            } else {
                $data[$key]['total'] += $item['num'];
                $data[$key]['data'][] = $item;
            }
        }

        return $data;
    }

    // 打印商品领料单
    public function printItemPickOrder(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');
        $sticks = Stick::where('task_no', $task_no)->with('picks')->get();
        if (count($sticks) == 0) return $this->sendData(402, '该单不存在');
        // 判断商品是否领料，如果没有就先领料
        $first = $sticks[0];
        if (in_array($first->status, [0, 2, 3])) {
            if ($msg = $this->pickItem($sticks, $task_no)) return $this->sendData(402, $msg);
        }
        $records = StickItemRecord::where(['task_no' => $task_no, 'type' => 1])->get();
        if (count($records) == 0) return $this->sendData(402, '导出错误');
        $fileName = $task_no . '商品领料单';
        return Excel::create($fileName, function ($excel) use ($records) {
            $excel->setTitle('商品领料单');
            $excel->sheet('商品领料', function ($sheet) use ($records) {
                $fr = $records[0];
                $data = [
                    ['任务类型：', '商品领料', '任务单号：', $fr->task_no],
                    ['发票号', '新产品代码', '产品代码', '中文名称', '制造记号', '效期', '领料数量', '库位', '托号'],
                ];
                foreach ($records as $record) {
                    $mark = $this->getMark($record->branch_mark, $record->case_mark, $record->box_mark);
                    $data[] = [$record->invoice_no, $record->material_code, $record->item_no, $record->item_name, $mark, $record->expired_at, $record->num, $record->location_no, $record->support_no];
                }
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
                $sheet->row(2, function ($row) {
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
                $sheet->getStyle('A1:I' . (count($records) + 2))->applyFromArray($style_array);
                $sheet->setWidth('A', 12);
                $sheet->setWidth('B', 12);
                $sheet->setWidth('C', 12);
                $sheet->setWidth('D', 30);
                $sheet->setWidth('E', 10);
                $sheet->setWidth('F', 10);
                $sheet->setWidth('G', 10);
                $sheet->setWidth('H', 10);
                // 合并单元格
                $sheet->mergeCells('D1:F1');
                // 样式设置
                $sheet->cells('A1:I' . (count($records) + 2), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
        })->export('xls');
    }

    // 商品分配
    private function pickItem($sticks, $task_no)
    {
        try {
            DB::connection('labelDB')->beginTransaction();
            $support_nos = [];
            foreach ($sticks as $stick) {
                $total = $stick->num;
                $search = [
                    'invoice_no' => $stick->invoice_no,
                    'material_code' => $stick->material_code,
                    'box_mark' => $stick->box_mark,
                    'case_mark' => $stick->case_mark,
                    'branch_mark' => $stick->branch_mark,
                    'expired_at' => $stick->expired_at,
                    'status' => 2,
                    'state' => 4
                ];
                $stocks = ItemStock::where($search)->get();
                foreach ($stocks as $stock) {
                    if ($total == 0) break;
                    $support_nos[] = $stock->support_no;
                    $insert = $insertRecord = [
                        'invoice_no' => $stock->invoice_no,
                        'item_no' => $stock->item_no,
                        'material_code' => $stock->material_code,
                        'item_name' => $stock->item_name,
                        'box_mark' => $stock->box_mark,
                        'case_mark' => $stock->case_mark,
                        'branch_mark' => $stock->branch_mark,
                        'valid_month' => $stock->valid_month,
                        'production_date' => $stock->production_date,
                        'expired_at' => $stock->expired_at,
                        'support_no' => $stock->support_no,
                        'status' => 2,
                    ];
                    // 插入库存
                    $insert['location_no'] = '暂存区';
                    $insert['state'] = 2;
                    $insertRecord['location_no'] = $stock->location_no;
                    $insertRecord['type'] = 1;
                    $insertRecord['task_no'] = $task_no;
                    if ($stock->num <= $total) {
                        $insert['num'] = $stock->num;
                        $insertRecord['num'] = $stock->num;
                        $stock->delete();
                        $total -= $stock->num;
                    } else {
                        $insert['num'] = $total;
                        $insertRecord['num'] = $total;
                        $stock->num -= $total;
                        $stock->save();
                        $total = 0;
                    }
                    $this->itemIn($insert);
                    StickItemRecord::create($insertRecord);
                }
                if ($total > 0) throw new \Exception($stick->item_no . '未贴标商品库存不足，请确认未贴标商品数量');
                $stick->status = ($stick->status == 0 ? 1 : ($stick->status == 2 ? 4 : 5));
                $stick->save();
            }
            $support_nos = array_unique($support_nos);
            ItemStock::whereIn('support_no', $support_nos)->update(['location_no' => '暂存区']);
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $e->getMessage();
        }
        $this->log(self::LOG_STICK, '单号' . $task_no . '完成商品领料');

        return false;
    }

    // 商品回退
    public function itemRollback(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');
        $sticks = Stick::where('task_no', $task_no)->with('picks')->get();
        if (count($sticks) == 0) return $this->sendData(402, '该单不存在');
        foreach ($sticks as $stick) {
            if (!in_array($stick->status, [1, 4])) return $this->sendData(402, '状态错误，无法回退');
        }
        // 开始回退
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($sticks as $stick) {
                $search = [
                    'task_no' => $task_no,
                    'invoice_no' => $stick->invoice_no,
                    'material_code' => $stick->material_code,
                    'box_mark' => $stick->box_mark,
                    'case_mark' => $stick->case_mark,
                    'branch_mark' => $stick->branch_mark,
                    'expired_at' => $stick->expired_at,
                    'type' => 1
                ];
                $records = StickItemRecord::where($search)->get();
                foreach ($records as $record) {
                    $s = [
                        'invoice_no' => $record->invoice_no,
                        'material_code' => $record->material_code,
                        'location_no' => $record->location_no,
                        'support_no' => $record->support_no,
                        'box_mark' => $stick->box_mark,
                        'case_mark' => $stick->case_mark,
                        'branch_mark' => $stick->branch_mark,
                        'expired_at' => $stick->expired_at,
                        'state' => 2,
                        'status' => 2
                    ];
                    $stock = ItemStock::where($s)->first();
                    if ($stock->num > $record->num) {
                        $insert = [
                            'invoice_no' => $stock->invoice_no,
                            'item_no' => $stock->item_no,
                            'material_code' => $stock->material_code,
                            'item_name' => $stock->item_name,
                            'box_mark' => $stock->box_mark,
                            'case_mark' => $stock->case_mark,
                            'branch_mark' => $stock->branch_mark,
                            'valid_month' => $stock->valid_month,
                            'production_date' => $stock->production_date,
                            'expired_at' => $stock->expired_at,
                            'support_no' => $stock->support_no,
                            'status' => 2,
                            'state' => 0,
                            'location_no' => '暂存区'
                        ];
                        $this->labelIn($insert);
                        $stock->num -= $record->num;
                        $stock->save();
                    } else {
                        $stock->state = 4;
                        $stock->save();
                    }
                    $insertRec = [
                        'invoice_no' => $stock->invoice_no,
                        'item_no' => $stock->item_no,
                        'material_code' => $stock->material_code,
                        'item_name' => $stock->item_name,
                        'box_mark' => $stock->box_mark,
                        'case_mark' => $stock->case_mark,
                        'branch_mark' => $stock->branch_mark,
                        'valid_month' => $stock->valid_month,
                        'production_date' => $stock->production_date,
                        'expired_at' => $stock->expired_at,
                        'support_no' => $stock->support_no,
                        'status' => 2,
                        'task_no' => $task_no,
                        'num' => $record->num,
                        'location_no' => $record->location_no
                    ];
                    StickItemRecover::create($insertRec);
                    $record->delete();
                }
                $stick->status = ($stick->status == 1 ? 0 : 2);
                $stick->save();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData($e->getMessage());
        }
        $this->log(self::LOG_STICK, '单号' . $task_no . '完成商品回退');

        return $this->sendData();
    }

    // 打印标签领料单
    public function printLabelPickOrder(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');
        $sticks = Stick::where('task_no', $task_no)->with('picks')->get();
        if (count($sticks) == 0) return $this->sendData(402, '该单不存在');
        // 判断商品是否领料，如果没有就先领料
        $first = $sticks[0];
        if (in_array($first->status, [0, 1])) {
            if ($msg = $this->pickLabel($sticks, $task_no)) return $this->sendData(402, $msg);
        }
        $records = StickLabelRecord::where(['task_no' => $task_no, 'type' => 1])->get();
        if (count($records) == 0) return $this->sendData(402, '导出错误');
        $tmp = [];
        foreach ($records as $record) {
            $key = $record->invoice_no . $record->location_no . $record->expired_at . $record->item_no . $record->mark;
            if (!isset($tmp[$key])) {
                $tmp[$key] = [
                    'num' => 0,
                    'record' => $record
                ];
            }
            $tmp[$key]['num'] += $record->num;
        }
        $records = $tmp;
        $fileName = $task_no . '标签领料单';
        return Excel::create($fileName, function ($excel) use ($records, $task_no) {
            $excel->setTitle('标签领料单');
            $excel->sheet('标签领料', function ($sheet) use ($records, $task_no) {
                $data = [
                    ['任务类型：', '标签领料', '任务单号：', $task_no],
                    ['发票号', '产品代码', '中文名称', '制造记号', '效期', '领料数量', '库位'],
                ];
                foreach ($records as $record) {
                    $total = $record['num'];
                    $record = $record['record'];
                    $data[] = [$record->invoice_no, $record->item_no, $record->label_name, $record->mark, $record->expired_at, $total, $record->location_no];
                }
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
                $sheet->row(2, function ($row) {
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
                $sheet->getStyle('A1:G' . (count($records) + 2))->applyFromArray($style_array);
                $sheet->setWidth('A', 12);
                $sheet->setWidth('B', 12);
                $sheet->setWidth('C', 30);
                $sheet->setWidth('D', 10);
                $sheet->setWidth('E', 10);
                $sheet->setWidth('F', 10);
                $sheet->setWidth('G', 10);
                // 合并单元格
                $sheet->mergeCells('D1:G1');
                // 样式设置
                $sheet->cells('A1:G' . (count($records) + 2), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
        })->export('xls');
    }

    // 标签分配
    private function pickLabel($sticks, $task_no)
    {
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($sticks as $stick) {
                $mark = $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark);
                $total = $stick->num;
                $search = [
                    'invoice_no' => $stick->invoice_no,
                    'item_no' => $stick->item_no,
                    'mark' => $mark,
                    'expired_at' => $stick->expired_at,
                    'status' => 1,
                    'type' => 0
                ];
                $stocks = LabelStock::where($search)->get();
                foreach ($stocks as $stock) {
                    if ($total == 0) break;
                    $insertRecord = [
                        'invoice_no' => $stock->invoice_no,
                        'item_no' => $stock->item_no,
                        'location_no' => $stock->location_no,
                        'label_name' => $stock->label_name,
                        'expired_at' => $stock->expired_at,
                        'mark' => $stock->mark,
                        'status' => 1,
                        'type' => 1,
                        'task_no' => $task_no,
                    ];
                    if ($stock->num <= $total) {
                        $insertRecord['num'] = $stock->num;
                        if ($stock->frozen_num == 0) {
                            $stock->delete();
                        } else {
                            $stock->num = 0;
                            $stock->save();
                        }
                        $total -= $stock->num;
                    } else {
                        $insertRecord['num'] = $total;
                        $stock->num -= $total;
                        $stock->save();
                        $total = 0;
                    }
                    StickLabelRecord::create($insertRecord);
                }
                if ($total > 0) {
                    $insert = [
                        'num' => 0 - $total,
                        'invoice_no' => $stick->invoice_no,
                        'item_no' => $stick->item_no,
                        'location_no' => '暂存区',
                        'label_name' => $stick->name,
                        'expired_at' => $stick->expired_at,
                        'mark' => $mark,
                        'status' => 1,
                        'type' => 0,
                    ];
                    $this->labelIn($insert);
                    $insert['task_no'] = $task_no;
                    $insert['type'] = 1;
                    $insert['num'] = $total;
                    StickLabelRecord::create($insert);
                }
                // 插入库存
                $insert = [
                    'invoice_no' => $stick->invoice_no,
                    'item_no' => $stick->item_no,
                    'location_no' => '暂存区',
                    'label_name' => $stick->name,
                    'mark' => $mark,
                    'expired_at' => $stick->expired_at,
                    'num' => $stick->num,
                    'status' => 1,
                    'type' => 2,
                ];
                $this->labelIn($insert);
                $stick->status = ($stick->status == 0 ? 2 : 4);
                $stick->save();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $e->getMessage();
        }
        $this->log(self::LOG_STICK, '单号' . $task_no . '完成标签分配');

        return false;
    }

    // 标签回退
    public function labelRollback(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');
        $sticks = Stick::where('task_no', $task_no)->with('picks')->get();
        if (count($sticks) == 0) return $this->sendData(402, '该单不存在');
        foreach ($sticks as $stick) {
            if (!in_array($stick->status, [2, 4])) return $this->sendData(402, '状态错误，无法回退');
        }
        // 开始回退
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($sticks as $stick) {
                $mark = $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark);
                $search = [
                    'task_no' => $task_no,
                    'invoice_no' => $stick->invoice_no,
                    'item_no' => $stick->item_no,
                    'mark' => $mark,
                    'expired_at' => $stick->expired_at,
                    'type' => 1
                ];
                $records = StickLabelRecord::where($search)->get();
                foreach ($records as $record) {
                    $s = [
                        'invoice_no' => $record->invoice_no,
                        'item_no' => $record->item_no,
                        'location_no' => '暂存区',
                        'mark' => $mark,
                        'expired_at' => $stick->expired_at,
                        'type' => 2,
                        'status' => 1
                    ];
                    $stock = LabelStock::where($s)->first();
                    $insert = [
                        'invoice_no' => $record->invoice_no,
                        'item_no' => $record->item_no,
                        'location_no' => $record->location_no,
                        'label_name' => $record->label_name,
                        'mark' => $mark,
                        'expired_at' => $record->expired_at,
                        'num' => $record->num,
                        'status' => 1,
                        'type' => 0,
                    ];
                    $this->labelIn($insert);
                    if ($stock->num > $record->num) {
                        $stock->num -= $record->num;
                        $stock->save();
                    } else {
                        $stock->delete();
                    }
                    $insertRec = [
                        'num' => $record->num,
                        'invoice_no' => $record->invoice_no,
                        'item_no' => $record->item_no,
                        'location_no' => $record->location_no,
                        'label_name' => $record->label_name,
                        'expired_at' => $record->expired_at,
                        'mark' => $mark,
                        'status' => 1,
                        'task_no' => $task_no,
                    ];
                    StickLabelRecover::create($insertRec);
                    $record->delete();
                }
                $stick->status = ($stick->status == 2 ? 0 : 1);
                $stick->save();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData($e->getMessage());
        }
        $this->log(self::LOG_STICK, '单号' . $task_no . '完成标签回退');

        return $this->sendData();
    }

    // 标签领料
    public function pick(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');
        $items = $request->get('items');
        if (empty($items) || !is_array($items)) return $this->sendData(402, '提交的数据不能为空');
        $sticks = Stick::where('task_no', $task_no)->with('picks')->get();
        if (count($sticks) == 0) return $this->sendData(402, '该单不存在或状态错误');
        $data = [];
        foreach ($items as $item) {
            if (empty($item['id'])) return $this->sendData(402, 'ID不能为空');
            if (empty($item['num']) || $item['num'] <= 0) {
                return ['code' => 1, 'message' => '领料数量必须大于0'];
            }
            if (!isset($data[$item['id']])) {
                $data[$item['id']] = 0;
            }
            $data[$item['id']] += $item['num'];
        }

        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($sticks as $stick) {
                if (isset($data[$stick->id])) {
                    if (!in_array($stick->status, [2, 3, 4, 5])) {
                        throw new \Exception("状态错误，无法领料");
                    }
                    $sum = StickPick::where('stick_id', $stick->id)->sum('num');
                    $insert = [
                        'stick_id' => $stick->id,
                        'num' => $data[$stick->id],
                        'invoice_no' => $stick->invoice_no,
                        'expired_at' => $stick->expired_at,
                        'mark' => $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark),
                        'item_no' => $stick->item_no,
                        'label_name' => $stick->name,
                    ];
                    StickPick::create($insert);
                    $insert = [
                        'invoice_no' => $stick->invoice_no,
                        'item_no' => $stick->item_no,
                        'location_no' => '暂存区',
                        'label_name' => $stick->name,
                        'mark' => $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark),
                        'expired_at' => $stick->expired_at,
                        'status' => 1,
                        'type' => 0,
                    ];
                    if ($sum >= $stick->num) {
                        $insert['num'] = 0 - $data[$stick->id];
                        $this->labelIn($insert);
                        $insert['num'] = abs($insert['num']);
                        $insert['type'] = 2;
                        $this->labelIn($insert);
                        // record
                        $insert['type'] = 1;
                        $insert['task_no'] = $task_no;
                        StickLabelRecord::create($insert);
                    } elseif ($sum < $stick->num && $sum + $data[$stick->id] > $stick->num) {
                        $insert['num'] = $stick->num - $sum - $data[$stick->id];
                        $this->labelIn($insert);
                        $insert['num'] = abs($insert['num']);
                        $insert['type'] = 2;
                        $this->labelIn($insert);
                        // record
                        $insert['type'] = 1;
                        $insert['task_no'] = $task_no;
                        StickLabelRecord::create($insert);
                    }
                    $stick->pick_num += $data[$stick->id];
                    $stick->status = (in_array($stick->status, [2, 3]) ? 3 : 5);
                    $stick->save();
                }
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_STICK, '单号' . $task_no . '标签领料');

        return sendData();
    }

    // 结束贴标单
    public function complete(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');

        $sticks = Stick::where('task_no', $task_no)->get();

        if (count($sticks) == 0) return $this->sendData(402, '该单不存在或状态错误');

        $act_man_power = $request->get('act_man_power');
        if ($act_man_power <= 0) return $this->sendData(402, '实际使用人力必须大于0');
        $endtime = $request->get('endtime');
        if (!$endtime || !strtotime($endtime)) return $this->sendData(402, '计划结束时间不能为空');
        $endtime = date('Y-m-d H:i:s', strtotime($endtime));
        $items = $request->get('items');
        if (empty($items) || !is_array($items)) return $this->sendData(402, '提交的数据不能为空');

        $res = $this->getOrganizedData($items, $sticks);

        if (!empty($res['code'])) return $this->sendData(402, $res['message']);

        $tmpData = $invoiceAndArrival = $itemData = [];
        foreach ($sticks as $stick) {
            if ($stick->status != 5) return $this->sendData(402, $stick->item_no . '未领料，无法提交');
            if ($stick->pick_num < $stick->num) return $this->sendData(402, $stick->item_no . '标签领料数量不能小于计划数量');
            $invoiceAndArrivalKey = $stick->invoice_no . '_' . $stick->item_no;
            $mark = $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark);
            $tmpDataKey = $invoiceAndArrivalKey . '_' . $mark;
            if (!isset($invoiceAndArrival[$invoiceAndArrivalKey])) {
                $s = ['invoice_no' => $stick->invoice_no, 'item_no' => $stick->item_no];
                $invoice = Invoice::where($s)->first();
                $arrival = LabelArrival::where($s)->first();
                $invoiceAndArrival[$invoiceAndArrivalKey] = [
                    'invoice' => $invoice,
                    'arrival' => $arrival,
                    'item_origin_num' => 0,
                    'item_submit_num' => 0,
                    'label_origin_num' => 0,
                    'label_submit_num' => 0,
                ];
            }
            if (!isset($tmpData[$tmpDataKey])) {
                $tmpData[$tmpDataKey] = [
                    'name' => $stick->name,
                    'invoice_no' => $stick->invoice_no,
                    'item_no' => $stick->item_no,
                    'expired_at' => $stick->expired_at,
                    'branch_mark' => $stick->branch_mark,
                    'case_mark' => $stick->case_mark,
                    'box_mark' => $stick->box_mark,
                    'item_origin_num' => 0,
                    'item_submit_num' => 0,
                    'label_origin_num' => 0,
                    'label_submit_num' => 0,
                ];
            }
            $tmpData[$tmpDataKey]['item_origin_num'] += $stick->num;
            $tmpData[$tmpDataKey]['label_origin_num'] += $stick->pick_num;
            $invoiceAndArrival[$invoiceAndArrivalKey]['item_origin_num'] += $stick->num;
            $invoiceAndArrival[$invoiceAndArrivalKey]['label_origin_num'] += $stick->pick_num;
        }
        $support_nos = $res['support_nos'];
        $res = $res['data'];

        // 商品确认数量 +
        // 标签确认数量+
        try {
            DB::connection('labelDB')->beginTransaction();
            // 把所有使用到的托盘上的其他商品移到暂存区
            ItemStock::whereIn('support_no', $support_nos)->update(['location_no' => '暂存区']);
            foreach ($sticks as $stick) {
                $mark = $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark);
                $ilKey = $stick->invoice_no . '_' . $stick->item_no;
                $resIndex = $tmpDataIndex =  $ilKey . '_' . $mark;
                // 先处理商品库存和记录表
                foreach (array_merge($res[$resIndex]['finished'], $res[$resIndex]['item_left']) as $item) {
                    $itemData[] = $item;
                    $tmpData[$tmpDataIndex]['item_submit_num'] += $item['num'];
                    $invoiceAndArrival[$ilKey]['item_submit_num'] += $item['num'];
                    $insert = [
                        'num' => $item['num'],
                        'invoice_no' => $stick->invoice_no,
                        'item_no' => $stick->item_no,
                        'location_no' => $item['location_no'],
                        'support_no' => $item['support_no'],
                        'material_code' => $stick->material_code,
                        'item_name' => $stick->name,
                        'box_mark' => $stick->box_mark,
                        'case_mark' => $stick->case_mark,
                        'branch_mark' => $stick->branch_mark,
                        'valid_month' => $stick->valid_month,
                        'production_date' => $stick->production_date,
                        'expired_at' => $stick->expired_at,
                        'marks' => $item['marks'],
                        'status' => $item['status'],
                        'state' => 0
                    ];
                    $this->itemIn($insert);
                    $insert['task_no'] = $task_no;
                    $insert['type'] = 0;
                    unset($insert['state']);
                    StickItemRecord::create($insert);
                }
                // 标签确认数量加一下已贴标数量
                foreach ($res[$resIndex]['finished'] as $item) {
                    $tmpData[$tmpDataIndex]['label_submit_num'] += $item['num'];
                    $invoiceAndArrival[$ilKey]['label_submit_num'] += $item['num'];
                }
                // 处理标签库存和记录表
                foreach (array_merge($res[$resIndex]['label_left'], $res[$resIndex]['abandoned']) as $item) {
                    $tmpData[$tmpDataIndex]['label_submit_num'] += $item['num'];
                    $invoiceAndArrival[$ilKey]['label_submit_num'] += $item['num'];
                    $insert = [
                        'num' => $item['num'],
                        'invoice_no' => $item['invoice_no'],
                        'item_no' => $item['item_no'],
                        'location_no' => $item['location_no'],
                        'label_name' => $stick->name,
                        'expired_at' => $stick->expired_at,
                        'mark' => $mark,
                        'type' => 0,
                        'status' => $item['status'],
                        'marks' => $item['marks']
                    ];
                    $this->labelIn($insert);
                    $insert['task_no'] = $task_no;
                    StickLabelRecord::create($insert);
                }
                // 更新贴标表
                $stick->act_man_power = $act_man_power;
                $stick->endtime = $endtime;
                $stick->finish_num = $res[$resIndex]['finished_total'];
                $stick->abandoned_num = $res[$resIndex]['abandoned_total'];
                $stick->item_left_num = $res[$resIndex]['item_left_total'];
                $stick->item_right_num = $res[$resIndex]['item_right_total'];
                $stick->label_left_num = $res[$resIndex]['label_left_total'];
                $stick->status = 6;
                $stick->save();
            }
            // 更新标签和商品库存和记录表
            foreach ($tmpData as $v) {
                // 商品库存
                $search = [
                    'invoice_no' => $v['invoice_no'],
                    'item_no' => $v['item_no'],
                    'expired_at' => $v['expired_at'],
                    'branch_mark' => $v['branch_mark'],
                    'case_mark' => $v['case_mark'],
                    'box_mark' => $v['box_mark'],
                    'status' => 2,
                    'state' => 2
                ];
                $stocks = ItemStock::where($search)->get();
                $total = $v['item_origin_num'];
                foreach ($stocks as $stock) {
                    if ($total == 0) break;
                    if ($stock->num <= $total) {
                        $stock->delete();
                        $total -= $stock->num;
                    } else {
                        $stock->num -= $total;
                        $stock->save();
                        $total = 0;
                    }
                }
                if ($total > 0) throw new \Exception($v['item_no'] . '商品库存不足，请确认未贴标商品数量');
                // 标签库存
                $search = [
                    'invoice_no' => $v['invoice_no'],
                    'item_no' => $v['item_no'],
                    'location_no' => '暂存区',
                    'mark' => $this->getMark($v['branch_mark'], $v['case_mark'], $v['box_mark']),
                    'status' => 1,
                    'type' => 2
                ];
                $stock = LabelStock::where($search)->first();
                $total = $v['label_origin_num'];
                $stock->num -= $total;
                if ($stock->num == 0) {
                    $stock->delete();
                } else {
                    $stock->save();
                }
            }
            // 更新标签到货明细、发票清单
            foreach ($invoiceAndArrival as $v) {
                $arrival = $v['arrival'];
                $arrival->confirm_num = $arrival->confirm_num - $v['label_origin_num'] + $v['label_submit_num'];
                $arrival->save();
                $invoice = $v['invoice'];
                $invoice->confirm_num = $invoice->confirm_num - $v['item_origin_num'] + $v['item_submit_num'];
                $invoice->save();
            }
            // 把托盘上的其他商品上架
            $this->stackOthers($itemData);
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $db = DB::connection('labelDB');
        $all  = $request->get('items');
        foreach ($all as $list) {
            $arr = [
                'invoice_no' => $list['invoice_no'],
                'item_no' => $list['item_no'],
            ];
            $item_stock = $db->table('item_stock')->where($arr)->where('status', '<>', '1')->orderBy('status', 'asc')->first();
            if ($item_stock->status == 3) {
                $db->table('commodity_inspection')->where($arr)->update(['status' => '已贴标']);
            }
        }

        $this->log(self::LOG_STICK, '单号' . $task_no . '完成贴标');
        return $this->sendData();
    }

    // 获取组织后的数据
    private function getOrganizedData($items, $sticks)
    {
        // 获取改单中所有的商品种类
        $exists = [];
        foreach ($sticks as $stick) {
            $mark = $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark);
            $key = $stick->invoice_no . '_' . $stick->item_no . '_' . $mark;
            if (!in_array($key, $exists)) $exists[] = $key;
        }
        // 开始验证
        $data = $checkItems = $support_nos = [];
        foreach ($items as $item) {
            if (empty($item['item_no'])) return ['code' => 1, 'message' => '商品代码不能为空'];
            if (empty($item['invoice_no'])) return ['code' => 1, 'message' => $item['item_no'] . '发票号不能为空'];
            if (empty($item['location_no'])) return ['code' => 1, 'message' => $item['item_no'] . '库位号不能为空'];
            if (empty($item['name'])) return ['code' => 1, 'message' => $item['item_no'] . '中文名称不能为空'];
            if (empty($item['material_code'])) return ['code' => 1, 'message' => $item['item_no'] . '物料编码不能为空'];
            if (!isset($item['expired_at'])) return ['code' => 1, 'message' => $item['item_no'] . '有效期字段必须存在'];
            // if (!isset($item['mark'])) return ['code' => 1, 'message' => $item['item_no'] . '制造记号字段必须存在'];
            if (empty($item['status']) || !in_array($item['status'], ['商品良品', '商品剩余', '标签剩余', '标签废品','商品次品'])) {
                return ['code' => 1, 'message' => $item['item_no'] . '状态错误'];
            }
            if (empty($item['num']) || $item['num'] <= 0) {
                return ['code' => 1, 'message' => $item['item_no'] . '数量必须大于0'];
            }
            $key = $item['invoice_no'] . '_' . $item['item_no'] . '_' . $item['mark'];
           
            if (!in_array($key, $exists)) return ['code' => 1, 'message' => $item['item_no'] . '该商品不在计划单内'];
            if (!isset($data[$key])) {
                $data[$key] = [
                    'invoice_no' => $item['invoice_no'],
                    'item_no' => $item['item_no'],
                    'expired_at' => $item['expired_at'],
                    'mark' => $item['mark'],
                    'name' => $item['name'],
                    'finished_total' => 0,
                    'abandoned_total' => 0,
                    'item_left_total' => 0,
                    'item_right_total' => 0,
                    'label_left_total' => 0,
                    'finished' => [],
                    'abandoned' => [],
                    'item_left' => [],
                    'label_left' => [],
                ];
            }
            if ($item['status'] == '商品良品') {
                if (empty($item['support_no'])) return ['code' => 1, 'message' => $item['item_no'] . '良品托号不能为空'];
                $item['status'] = 3;
                $data[$key]['finished_total'] += $item['num'];
                $data[$key]['finished'][] = $item;
                $checkItems[] = $item;
                $support_nos[] = $item['support_no'];
            } elseif ($item['status'] == '商品剩余') {
                if (empty($item['support_no'])) return ['code' => 1, 'message' => $item['item_no'] . '商品剩余托号不能为空'];
                $item['status'] = 2;
                $data[$key]['item_left_total'] += $item['num'];
                $data[$key]['item_left'][] = $item;
                $checkItems[] = $item;
                $support_nos[] = $item['support_no'];
            } elseif ($item['status'] == '商品次品') {
                if (empty($item['support_no'])) return ['code' => 1, 'message' => $item['item_no'] . '商品次品托号不能为空'];
                $item['status'] = 4;
                $data[$key]['item_right_total'] += $item['num'];
                $data[$key]['item_left'][] = $item;
                $checkItems[] = $item;
                $support_nos[] = $item['support_no'];
            }elseif ($item['status'] == '标签废品') {
                $item['status'] = 3;
                $data[$key]['abandoned_total'] += $item['num'];
                $data[$key]['abandoned'][] = $item;
            } else {
                $item['status'] = 1;
                $data[$key]['label_left_total'] += $item['num'];
                $data[$key]['label_left'][] = $item;
            }
        }
        if (count($exists) != count($data)) return ['code' => 1, 'message' => '提交的商品类型数量不符'];
        // 检验托号和库位
        if ($msg = $this->checkSupport($checkItems)) {
            return ['code' => 1, 'message' => $msg];
        }

        return ['code' => 0, 'data' => $data, 'support_nos' => $support_nos];
    }

    // 终止计划单
    public function terminate(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '单号不能为空');

        $sticks = Stick::where(['task_no' => $task_no, 'status' => 0])->get();
        if (!$sticks) return $this->sendData(402, '该单不存在或已开始');
        $res = Stick::where('task_no', $task_no)->get();
        foreach ($res as $val) {
            $data = [
                'item_no' => $val['item_no'],
                'invoice_no' => $val['invoice_no'],
                'box_mark' => $val['box_mark'],
                'case_mark' => $val['case_mark'],
                'branch_mark' => $val['branch_mark'],
                'valid_month' => $val['valid_month'],
                'num' => $val['num'],
                'expired_at' => $val['expired_at'],
                'state' => 4,
            ];
            $item = ItemStock::where($data)->first();
            if (!$item) {
                return $this->sendData(402, '终止失败，该单不存在或已开始');
            }
            $item->state = 0;
            $item->save();
        }
        Stick::where('task_no', $task_no)->update(['status' => 7]);
        $this->log(self::LOG_STICK, '单号' . $task_no . '异常终止');

        return $this->sendData();
    }

    // 导出
    public function export(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return '发票号不能为空';

        $sticks = Stick::where('task_no', $task_no)->get();
        if (count($sticks) == 0) return '单号错误';
        $error = '';
        foreach ($sticks as $stick) {
            $item = Item::where('material_code', $stick->material_code)->first();
            if (!$item) {
                $error .= $stick->item_no . "无商品基础信息，无法导出<br>";
            } else {
                $stick->item = $item;
            }
        }
        if ($error) return $error;
        $fileName = '加工记录表';
        return Excel::create($fileName, function ($excel) use ($sticks) {
            $excel->setTitle('盖章导出');
            foreach ($sticks as $stick) {
                $mark = $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark);
                $sheetName = $stick->item_no . '_' . $mark . '_' . str_replace('/', '-', $stick->invoice_no);
                $excel->sheet($sheetName, function ($sheet) use ($stick) {
                    $mark = $this->getMark($stick->branch_mark, $stick->case_mark, $stick->box_mark);
                    $item = $stick->item;
                    // 获取制造记号
                    $data = [
                        ['加工记录表'],
                        ['产品基本信息', '货品编码', '中文名', '', '制造记号', '效期', '数量/pcs', '箱入数', "盒入数", '制造记号', '效期', '数量/pcs'],
                        [$stick->invoice_no, $stick->item_no, $stick->name, '', $mark, date('Y年m月', strtotime($stick->expired_at)), $stick->num],
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
    public function cartdel(Request $request)
    {
        $ids = $request->get('ids');
        foreach ($ids as $key) {
            $res = ItemStock::where('id', $key)->first();
            $data = [
                'invoice_no' => $res->invoice_no,
                'item_no' => $res->item_no,
                'support_no' => $res->support_no,
                'location_no' => $res->location_no,
                'box_mark' => $res->box_mark,
                'case_mark' => $res->case_mark,
                'branch_mark' => $res->branch_mark,
                'valid_month' => $res->valid_month,
                'expired_at' => $res->expired_at,
                'status' => 2,
                'state' => 0,
            ];
            $item = ItemStock::where($data)->first();
            if ($item) {
                $item->num += $res->num;
                $item->save();
                $res->delete();
            } else {
                $res->state = 0;
                $res->save();
            }
        }
        return $this->sendData();
    }
    public function exportBooks(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no) return $this->sendData(402, '发票号不能为空');
        $sticks = Stick::where('task_no', $task_no)->get();
        if (count($sticks) == 0) return '单号错误';
        $image = [];
        foreach($sticks as $val){
            $img = Item::where('material_code',$val->material_code)->first();
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
}
