<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\LabelStock;
use Modules\Label\Models\LabelArrival;
use Modules\Label\Models\Location;
use Modules\Label\Models\AbandonedLabel;
use Modules\Label\Models\Item;
use Modules\Label\Models\Invoice;

class LabelStockController extends StockController
{
    // 传递type和model
    public function __construct()
    {
        parent::__construct();
        $this->listModel = new LabelArrival;
        $this->stockModel = new LabelStock;
        $this->Invoice = new Invoice;
        $this->type = 'label';
    }

    // 分页获取库存列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('location_no')) $where[] = ['location_no', '=', $request->get('location_no')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        if ($request->get('mark')) $where[] = ['mark', '=', $request->get('mark')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('status') === '0' || in_array($request->get('status'), [1, 2, 3])) {
            $where[] = ['status', '=', $request->get('status')];
        }
        if ($request->get('statusIn')) {
            $status = explode(',', $request->get('statusIn'));
            $stocks = LabelStock::where($where)->whereIn('status', $status)->orderBy($sort, 'desc')->paginate($limit);
        } else {
            $stocks = LabelStock::where($where)->orderBy($sort, 'desc')->paginate($limit);
        }

        return $this->sendData(200, '', $stocks);
    }

    // 分页获取列表
    public function stockList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
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
            $goodsnum = 0;
            $labelnum = 0;
            $sum = 0;
            $invoice_no = $this->Invoice::where('invoice_no', $item->invoice_no)->get();
            foreach ($invoice_no as $invoice) {
                $num = $this->Invoice::where('invoice_no', $invoice->invoice_no)->where('item_no', $invoice->item_no)->first();
                $invoices = $this->listModel::where('invoice_no', $invoice->invoice_no)->where('item_no', $invoice->item_no)->first();
                if(!$invoices){
                    $confirm_num = 0;
                }else{
                    $confirm_num = $invoices->confirm_num;
                }
                if (!$num) {
                    $goodsnum = 0;
                    $labelnum = 0;
                    $sum = 0;
                } else {
                    $sum += $num->num;
                    if ($num->num <= $confirm_num) {
                        $goodsnum += $num->num;
                        $labelnum += 1;
                    }
                }
            }

            if ($labelnum == 0) {
                $res = 0;
            } else {
                $res = $labelnum / count($invoice_no);
            }
            if ($goodsnum == 0) {
                $resule = 0;
            } else {
                $resule = $goodsnum / $sum;
            }
            $item->res = round($res, 2) * 100 . '%';
            $item->resule = round($resule, 2) * 100 . '%';
            $item->status = min(explode(',', $item->status));
        }

        return $this->sendData(200, '', $items);
    }

    //根据发票号获取一条数据
    public function getByNo(Request $request)
    {
        $invoice_no = $request->get('invoice_no');
        if (!$invoice_no) return $this->sendData(402, '发票号不能为空');
        $arrivals = $this->uGetByNo($invoice_no);
        foreach ($arrivals as $arrival) {
            $arrival->item = Item::where('item_no', $arrival->item_no)->first();
        }

        return $this->sendData(200, '', $arrivals);
    }

    // 标签上架
    public function stock(Request $request)
    {
        $items = $request->all();
        if (empty($items) || !is_array($items)) return $this->sendData(402, '提交数据为空');
        // 先组织一下数据，防止多次查询sql
        $res = $this->getOrganizedData($items);
        $invoice_no = $items[0]['invoice_no'];
        if (!empty($res['code'])) return $this->sendData(402, $res['message']);
        try {
            $locations = Location::where(['type' => 1, 'label_type' => 1])->pluck('location_no')->toArray();
            $defaultLocations = Location::where(['type' => 1, 'label_type' => 0])->pluck('location_no')->toArray();
            DB::connection('labelDB')->beginTransaction();
            foreach ($res['data'] as $item) {
                $arrival = $item['stock'];
                foreach ($item['data'] as $value) {
                    $arrival->confirm_num += $value['num'];
                    if ($value['invoice_no'] != $invoice_no) {
                        throw new \Exception($value['item_no'] . '发票号不一致');
                    }
                    $insert = [
                        'num' => $value['num'],
                        'invoice_no' => $arrival->invoice_no,
                        'item_no' => $arrival->item_no,
                        'location_no' => $value['location_no'],
                        'label_name' => $arrival->label_name,
                        'expired_at' => '',
                        'mark' => '',
                        'status' => 0,
                        'type' => 0,
                    ];
                    if (in_array($value['location_no'], $locations)) {
                        $insert['status'] = 1;
                    } elseif (!in_array($value['location_no'], $defaultLocations)) {
                        throw new \Exception($value['item_no'] . '库位必须为未盖章或已盖章');
                    }
                    $this->labelIn($insert);
                }
                $arrival->stocktime = date('Y-m-d');
                $arrival->status = 1;
                $arrival->save();
            }

            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_STACK, '入库' . '发票号' . $invoice_no . '的商品标签');

        return $this->sendData();
    }

    // 组织和验证一下数据
    private function getOrganizedData($items)
    {
        $data = $this->organizeData($items, 'label');
        if (!empty($data['code'])) return $data;
        foreach ($data as $key => $value) {
            $search = [
                'invoice_no' => $value['data'][0]['invoice_no'],
                'item_no' => $value['data'][0]['item_no'],
            ];
            $item = LabelArrival::where($search)->first();
            if (!$item) return ['code' => 1, 'message' => $value['data'][0]['item_no'] . '标签到货明细不存在'];
            $data[$key]['stock'] = $item;
        }

        return ['code' => 0, 'data' => $data];
    }

    // 标签库存冻结
    public function freeze(Request $request)
    {
        $params = $request->all();
        if (empty($params) || !is_array($params)) {
            return $this->sendData(402, '提交的数据格式错误');
        }
        $total = 0;
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($params as $item) {
                $total += $item['frozen_num'];
                if (empty($item['id']) || !$stock = LabelStock::find($item['id'])) throw new \Exception('ID不能为空');
                if (empty($item['frozen_num']) || $item['frozen_num'] <= 0) throw new \Exception('冻结数量必须大于0');
                if ($stock->num < $item['frozen_num']) throw new \Exception('冻结数量超过限制');
                if ($stock->status == 2 || $stock->status == 3) throw new \Exception('报废标签无法冻结');
                if ($stock->type != 0) throw new \Exception('标签非可用');
                $stock->num -= $item['frozen_num'];
                $stock->frozen_num += $item['frozen_num'];
                $stock->save();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_FROZE, '冻结' . '数量为' . $total . '的商品标签');

        return $this->sendData();
    }

    // 标签库存解冻
    public function unfreeze(Request $request)
    {
        $params = $request->all();
        if (empty($params) || !is_array($params)) {
            return $this->sendData(402, '提交的数据格式错误');
        }
        $total = 0;
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($params as $item) {
                $total += $item['frozen_num'];
                if (empty($item['id']) || !$stock = LabelStock::find($item['id'])) throw new \Exception('ID不能为空');
                if (empty($item['frozen_num']) || $item['frozen_num'] <= 0) throw new \Exception('解冻数量必须大于0');
                if ($stock->frozen_num < $item['frozen_num']) throw new \Exception('解冻数量超过限制');
                $stock->num += $item['frozen_num'];
                $stock->frozen_num -= $item['frozen_num'];
                $stock->save();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_FROZE, '解冻' . '数量为' . $total . '的商品标签');

        return $this->sendData();
    }

    //已处理的废标列表
    public function abandonedIndex(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('location_no')) $where[] = ['location_no', '=', $request->get('location_no')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        if ($request->get('mark')) $where[] = ['mark', '=', $request->get('mark')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('status') === '0' || in_array($request->get('status'), [1, 2, 3])) {
            $where[] = ['status', '=', $request->get('status')];
        }

        $stocks = AbandonedLabel::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $stocks);
    }

    //废标列表
    public function abandonedList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', '=', $request->get('invoice_no')];
        if ($request->get('location_no')) $where[] = ['location_no', '=', $request->get('location_no')];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];

        $stocks = LabelStock::where(function ($q) use ($where) {
            $q->where($where)->whereIn('status', [2, 3]);
        })
            ->orWhere(array_merge($where, [['frozen_num', '>', 0], ['type', '=', 0]]))
            ->orderBy($sort, 'desc')
            ->paginate($limit);

        return $this->sendData(200, '', $stocks);
    }

    //废标处理
    public function abandonedSubmit(Request $request)
    {
        $items = $request->all();
        foreach ($items as $item) {
            if (empty($item['id'])) return $this->sendData(402, 'ID不能为空');
            if (empty($item['num']) || $item['num'] <= 0) return $this->sendData(402, '数量不能小于0');
        }
        $total = 0;
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($items as $item) {
                $total += $item['num'];
                $stock = LabelStock::find($item['id']);
                if (!$stock) throw new \Exception('库存错误，请刷新页面重新提交');
                if ($stock->type) throw new \Exception('标签非可用，无法废弃');
                if ($stock->status == 2 || $stock->status == 3) {
                    if ($stock->num < $item['num']) throw new \Exception('废弃数量超过限制');
                    $stock->num -= $item['num'];
                } else {
                    if ($stock->frozen_num < $item['num']) throw new \Exception('废弃数量超过限制');
                    $stock->frozen_num -= $item['num'];
                }
                $insert = [
                    'invoice_no' => $stock->invoice_no,
                    'item_no' => $stock->item_no,
                    'location_no' => $stock->location_no,
                    'expired_at' => $stock->expired_at,
                    'mark' => $stock->mark,
                    'num' => $item['num'],
                    'status' => $stock->status
                ];
                AbandonedLabel::create($insert);
                if ($stock->num == 0 && $stock->frozen_num == 0) {
                    $stock->delete();
                } else {
                    $stock->save();
                }
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            DB::connection('labelDB')->rollBack();
            Log::info($e->getMessage());
            return $this->sendData(402, $e->getMessage());
        }
        $this->log(self::LOG_ABANDON, '废弃' . '数量为' . $total . '的商品标签');

        return $this->sendData();
    }
}
