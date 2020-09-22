<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\ItemStock;
use Modules\Label\Models\Invoice;
use Modules\Label\Models\Stage;

class ItemStockController extends StockController
{
    // 传递type和model
    public function __construct()
    {
        parent::__construct();
        $this->listModel = new Invoice;
        $this->stockModel = new ItemStock;
        $this->type = 'item';
    }

    // 分页获取库存列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if($request->get('status') === '0' || in_array($request->get('status'), ['stage', '未贴标', '已贴标'])){
            if($request->get('status') == 'stage') $status = 1;
            if($request->get('status') == '未贴标') $status = 2;
            if($request->get('status') == '已贴标') $status = 3;
        }else{
            $status = 0;
        }
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('support_no')) $where[] = ['support_no', '=', $request->get('support_no')];
        if ($request->get('location_no')) $where[] = ['location_no', '=', $request->get('location_no')];
        if ($request->get('branch_mark')) $where[] = ['branch_mark', '=', $request->get('branch_mark')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('status')) $where[] = ['status', '=', $status];
        // if ($request->get('status') === '0' || in_array($request->get('status'), [1, 2, 3])) {
        //     $where[] = ['status', '=', $request->get('status')];
        // }
        $stocks = ItemStock::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $stocks);
    }

    // 分页获取发票清单
    public function stockList(Request $request)
    {
        $data = $this->uStockList($request);

        return $this->sendData(200, '', $data);
    }

    //根据ID获取库存
    public function getByIds(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) return $this->sendData(402, 'ID不能为空');
        if (!is_array($ids)) return $this->sendData(402, 'ID必须是一个数组');
        $stocks = ItemStock::whereIn('id', $ids)->get();

        return $this->sendData(200, '', $stocks);
    }

    //根据发票号获取一条数据
    public function getByNo(Request $request)
    {
        $invoice_no = $request->get('invoice_no');
        if (!$invoice_no) return $this->sendData(402, '发票号不能为空');
        $invoices = $this->uGetByNo($invoice_no);

        return $this->sendData(200, '', $invoices);
    }

    // 商品stage
    public function stage(Request $request)
    {
        $items = $request->all();
        if (empty($items) || !is_array($items)) return $this->sendData(402, '提交数据为空');
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($items as $item) {
                if (empty($item['id'])) throw new \Exception('stage的ID不能为空');
                $invoice = Invoice::findOrFail($item['id']);
                if (!$invoice || $invoice->status != 0) throw new \Exception('发票单状态错误');
                $insert = [
                    'num' => $invoice->num,
                    'invoice_no' => $invoice->invoice_no,
                    'item_no' => $invoice->item_no,
                    'location_no' => '暂存区',
                    'support_no' => '',
                    'item_name' => $invoice->material_desc,
                    'material_code' => $invoice->material_code,
                    'status' => 1,
                    'box_mark' => '',
                    'case_mark' => '',
                    'branch_mark' => '',
                    'state' => 0
                ];
                $this->itemIn($insert);
                $invoice->stage_num = $invoice->num;
                $invoice->stocktime = date('Y-m-d');
                $invoice->status = 1;
                $invoice->save();
            }

            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_STACK, 'stage' . '商品');

        return $this->sendData();
    }

    // 已处理的stage库存列表
    public function stageIndex(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];

        $stocks = Stage::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $stocks);
    }

    //stage列表
    public function stageList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [
            ['status', '=', 1],
        ];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];

        $stocks = ItemStock::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $stocks);
    }

    //stage处理
    public function stageSubmit(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) return $this->sendData(402, 'ID不能为空');
        if (!is_array($ids)) $ids = explode(',', $ids);
        $stocks = ItemStock::whereIn('id', $ids)->get();
        if (count($stocks) == 0) return $this->sendData(402, 'ID错误');
        foreach ($stocks as $stock) {
            if ($stock->status != 1) return $this->sendData(402, '库存状态错误');
        }
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($stocks as $stock) {
                $insert = [
                    'invoice_no' => $stock->invoice_no,
                    'item_no' => $stock->item_no,
                    'material_code' => $stock->material_code,
                    'num' => $stock->num,
                    'item_name' => $stock->item_name
                ];
                Stage::create($insert);
                $stock->delete();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_ABANDON, '废弃商品stage');

        return $this->sendData();
    }
}
