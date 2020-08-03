<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Label\Models\ItemMove;

class ItemMoveController extends BaseController
{
    // 分页获取标签移库信息
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('task_no')) $where[] = ['task_no', '=', $request->get('task_no')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        if ($request->get('branch_mark')) $where[] = ['branch_mark', '=', $request->get('branch_mark')];
        if ($request->get('case_mark')) $where[] = ['case_mark', '=', $request->get('case_mark')];
        if ($request->get('box_mark')) $where[] = ['box_mark', '=', $request->get('box_mark')];
        $moves = ItemMove::where($where)->with('user')->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $moves);
    }

    // 获取库存列表
    // public function getList(Request $request)
    // {
    //     $limit = $request->get('limit') ? $request->get('limit') : 20;
    //     $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
    //     $where = [];
    //     if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
    //     if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
    //     $stocks = ItemStock::where($where)->orderBy($sort, 'desc')->paginate($limit);

    //     return $this->sendData(200, '', $stocks);
    // }

    // 移库提交
    // public function submit(Request $request)
    // {
    //     $items = $request->get('items');
    //     foreach ($items as $item) {
    //         if (empty($item['id'])) return $this->sendData(402, 'ID不能为空');
    //         if (empty($item['location_no'])) return $this->sendData(402, '库位号不能为空');
    //         if (empty($item['support_no'])) return $this->sendData(402, '托号不能为空');
    //         if (empty($item['num']) || $item['num'] > 0) return $this->sendData(402, '数量不能小于0');
    //     }

    //     try {
    //         DB::connection('labelDB')->beginTransaction();
    //         foreach ($items as $item) {
    //             $stock = LabelStock::find($item['id']);
    //             if (!$stock) throw new \Exception('库存信息发生变化，请刷新重试');
    //             if ($stock->num < $item['num']) throw new \Exception('移库数量不能超过库存数量');
    //             if ($stock->location_no == $item['location_no'] && $stock->support_no == $item['support_no']) continue;
    //             $insert = [
    //                 'invoice_no' => $stock->invoice_no,
    //                 'item_no' => $stock->item_no,
    //                 'location_no' => $stock->location_no,
    //                 'support_no' => $stock->support_no,
    //                 'material_code' => $stock->material_code,
    //                 'item_name' => $stock->item_name,
    //                 'box_mark' => $stock->box_mark,
    //                 'case_mark' => $stock->case_mark,
    //                 'branch_mark' => $stock->branch_mark,
    //                 'valid_month' => $stock->valid_month,
    //                 'production_date' => $stock->production_date,
    //                 'expired_at' => $stock->expired_at,
    //                 'status' => 2,
    //                 'type' => 1,
    //                 'task_no' => $task_no,
    //             ];
    //             if ($stock->num <= $total) {
    //                 $insert['num'] = $stock->num;
    //                 StickItemRecord::create($insert);
    //                 $stock->delete();
    //                 $total -= $stock->num;
    //             } else {
    //                 $insert['num'] = $total;
    //                 StickItemRecord::create($insert);
    //                 $stock->num -= $total;
    //                 $stock->save();
    //                 $total = 0;
    //             }
    //         }
    //         DB::connection('labelDB')->commit();
    //     } catch (\Exception $e) {
    //         DB::connection('labelDB')->rollBack();
    //         Log::info($e->getMessage());
    //         return $this->sendData(402, $e->getMessage());
    //     }
    //     $this->log(self::LOG_MOVE, '移库完成');

    //     return $this->sendData();
    // }

    // 根据任务编号获取任务
    public function getByNo(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no || !$task = ItemMove::where('task_no', $task_no)->get()) {
            return $this->sendData(402, '单号错误');
        }

        return $this->sendData(200, '', $task);
    }
}
