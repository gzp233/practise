<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends BaseController
{
    protected $listModel = null;
    protected $stockModel = null;
    protected $type = '';

    // 分页获取列表
    protected function uStockList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        $columns = ['invoice_no', DB::raw('sum(num) as total'), DB::raw('sum(confirm_num) as confirm_total'), DB::raw('max(created_at) as created_at'), DB::raw('group_concat(status) as status')];
        $items = $this->listModel::where($where)
            ->select($columns)
            ->groupBy('invoice_no')
            ->orderBy($sort, 'desc')
            ->paginate($limit);
        //  统计状态和数量
        foreach ($items as $item) {
            $item->status = min(explode(',', $item->status));
        }

        return $items;
    }

    //根据ID获取一条数据
    protected function uGetByNo($invoice_no)
    {
        $orders = $this->listModel::where(['invoice_no' => $invoice_no])->get();

        return $orders;
    }
}
