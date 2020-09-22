<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\LabelStock;
use Modules\Label\Models\LabelMove;
use Modules\Label\Models\Location;

class LabelMoveController extends BaseController
{
    // 分页获取标签移库信息
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('task_no')) $where[] = ['task_no', '=', $request->get('task_no')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        if ($request->get('mark')) $where[] = ['mark', '=', $request->get('mark')];
        $moves = LabelMove::where($where)->with('user')->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $moves);
    }

    // 获取库存列表
    public function getList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [
            ['type', '=', 0],
        ];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        $stocks = LabelStock::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $stocks);
    }

    // 移库提交
    public function submit(Request $request)
    {
        $items = $request->get('items');
        foreach ($items as $item) {
            if (empty($item['id'])) return $this->sendData(402, 'ID不能为空');
            if (empty($item['location_no'])) return $this->sendData(402, '库位号不能为空');
            if (empty($item['num']) || $item['num'] <= 0) return $this->sendData(402, '数量不能小于0');
        }

        try {
            DB::connection('labelDB')->beginTransaction();
            $task_no = $this->genId();
            foreach ($items as $item) {
                $stock = LabelStock::find($item['id']);
                if (!$stock) throw new \Exception('库存信息发生变化，请刷新重试');
                if ($stock->num < $item['num']) throw new \Exception($stock->item_no . '移库数量不能超过库存数量');
                if ($stock->location_no == $item['location_no']) continue;
                // 验证库位类型是否一致
                $compare = Location::where('type', 1)->whereIn('location_no', [$stock->location_no, $item['location_no']])->get()->toArray();
                if (count($compare) != 2 || $compare[0]['label_type'] != $compare[1]['label_type']) {
                    throw new \Exception($stock->item_no . '库位状态不一致');
                }

                $insert = [
                    'invoice_no' => $stock->invoice_no,
                    'item_no' => $stock->item_no,
                    'from_location_no' => $stock->location_no,
                    'to_location_no' => $item['location_no'],
                    'label_name' => $stock->label_name,
                    'expired_at' => $stock->expired_at,
                    'mark' => $stock->mark,
                    'num' => $item['num'],
                    'status' => $stock->status,
                    'task_no' => $task_no,
                    'user_id' => $this->user->id,
                ];
                LabelMove::create($insert);
                unset($insert['task_no'], $insert['from_location_no'], $insert['to_location_no'], $insert['user_id']);
                $insert['location_no'] = $item['location_no'];
                $insert['type'] = 0;
                $this->labelIn($insert);
                if ($stock->num == $item['num'] && $stock->frozen_num == 0) {
                    $stock->delete();
                } else {
                    $stock->num -= $item['num'];
                    $stock->save();
                }
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_MOVE, '移库完成');

        return $this->sendData();
    }

    // 根据任务编号获取任务
    public function getByNo(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no || !$task = LabelMove::where('task_no', $task_no)->get()) {
            return $this->sendData(402, '单号错误');
        }

        return $this->sendData(200, '', $task);
    }
}
