<?php

namespace App\Http\Controllers\Instorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Instorage\SalesReturn;
use App\Models\Instorage\SalesReturnTag;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Storage\Goods;
use Excel;

class SalesReturnController extends InBaseController
{
    protected $rules = [];

    /**
     * Create a new SalesReturnController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->user = auth()->user();
    }

    /**
     * 分页获取退货入库指令
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = [];
        if ($request->get('OrderNo')) $params[] = ['o.OrderNo', 'like', '%' . $request->get('OrderNo')];
        if ($request->get('InStcNo')) $params[] = ['o.InStcNo', 'like', '%' . $request->get('InStcNo')];
        if ($request->get('FineFlg')) $params[] = ['o.FineFlg', 'like', '%' . $request->get('FineFlg')];
        if ($request->get('state')) $params[] = ['o.state', '=', $request->get('state')];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['o.created_at', '>=', $starttime];
            $params[] = ['o.created_at', '<=', $endtime];
        }
        if ($request->get('time') && $request->get('time')[0] && $request->get('time')[0] != 'null') {
            $stime = date('Y-m-d H:i:s', strtotime($request->get('time')[0]));
            $etime = date('Y-m-d H:i:s', strtotime($request->get('time')[1]));
            $params[] = ['o.time', '>=', $stime];
            $params[] = ['o.time', '<=', $etime];
        }
        $data = DB::table('ret_in_dirt as o')
            ->select('o.OrderNo', 'o.InStcNo', 'd.DeliverAdd', 'o.state', 'o.AUART', 'o.FineFlg', 'c.ShopSignNM','o.time', DB::raw('MAX(o.created_at) as created_at'))
            ->where($params)
            ->orderBy('o.OrderNo', 'desc')
            ->groupBy('o.OrderNo', 'o.InStcNo', 'd.DeliverAdd', 'o.state', 'o.AUART', 'o.FineFlg', 'o.time', 'c.ShopSignNM')
            ->leftJoin('customer as c', 'o.CustomerCd', '=', 'c.CUSTOMERCD')
            ->leftJoin('deliver_address as d', 'o.DeliverAddCD', '=', 'd.DeliverAddCD')
            ->paginate($limit);
        // 查询是否入库完成
        foreach ($data as $key => $item) {
            $data[$key]->is_finished = 1;
            $data[$key]->is_confirmed = 1;
            $res = SalesReturn::where('OrderNo', $item->OrderNo)->with('tag')->get();
            foreach ($res as $value) {
                if (!$value->tag || $value->tag->number != $value->AdmQnty) $data[$key]->is_finished = 0;
                if ($value->tag && $value->tag->confirmNum < $value->tag->number) $data[$key]->is_confirmed = 0;
            }
        }

        return sendData(200, '', $data);
    }

    public function getById(Request $request)
    {
        $salesReturns = SalesReturn::where('OrderNo', $request->get('id'))
            ->with(['product', 'customer', 'deliver', 'tag'])->get();
        foreach ($salesReturns as $key => $order) {
            if(isset($order->tag->confirmNum) && $order->tag->confirmNum == $salesReturns[$key]->AdmQnty){
                $salesReturns[$key]->todoNumber = 0;
                $salesReturns[$key]->query = 0;
                $salesReturns[$key]->library = 0;
                $salesReturns[$key]->process = 0;
                $salesReturns[$key]->confirmNum = $salesReturns[$key]->AdmQnty;
            }else {
                if ($order->tag) {
                    $query = ['product_id' => $order->product->id, 'odd' => $order->OrderNo];
                    $process = ['stock_no' => '加工区', 'state_name' => 'C302', 'product_id' => $order->product->id, 'odd' => $order->OrderNo];
                    $stock = ['origin_stock_no' => '加工区', 'product_id' => $order->product->id, 'odd' => $order->OrderNo];
//                $where = ['state_name' => 'C302', 'product_id' => $order->product->id,];
//                $library = GoodsRecord::where($where)->where('stock_no', '<>', '加工区')->where('odd', $order->InvoiceNo)->sum('number');
                    $process = GoodsRecord::where($process)->sum('number');

                    $origin = GoodsRecord::where($stock)->sum('number');
                    $toConfirmNumber = GoodsRecord::where($query)->where('type','<>','instorage_process')->where('state_name','<>','C302')->sum('number');
                    $left = $order->tag->number - $order->tag->confirmNum;
                    $salesReturns[$key]->toConfirmNumber = $toConfirmNumber > $left ? $left : $toConfirmNumber;
                    $salesReturns[$key]->todoNumber = $salesReturns[$key]->AdmQnty - $order->tag->number;
                    $salesReturns[$key]->query = $toConfirmNumber - $order->tag->confirmNum;
                    $salesReturns[$key]->confirmNum = $order->tag->confirmNum;
                    $salesReturns[$key]->process = $process - $origin;
                    $salesReturns[$key]->library = $salesReturns[$key]->AdmQnty - $salesReturns[$key]->todoNumber - $salesReturns[$key]->query - $order->tag->confirmNum - $salesReturns[$key]->process;
                } else {
                    $salesReturns[$key]->todoNumber = $salesReturns[$key]->AdmQnty;
                    $salesReturns[$key]->query = 0;
                    $salesReturns[$key]->library = 0;
                    $salesReturns[$key]->process = 0;
                    $salesReturns[$key]->confirmNum = 0;
                }
            }
        }

        return sendData(200, '', $salesReturns);
    }

    public function hasStocked(Request $request)
    {
        $id = $request->get('id');
        $product_id = $request->get('product_id');
        $params = [
            ['related_id', '=', $id],
            ['type', '=', 'ret_in_dirt'],
            ['product_id', '=', $product_id],
        ];
        $goods = GoodsRecord::where($params)
            ->orderBy('CHARG', 'asc')
            ->with(['product'])
            ->get();

        return sendData(200, '', $goods);
    }

    public function stockIn(Request $request)
    {
        $params = $request->all();
        $error = $this->inStorage($params, new SalesReturnTag(), 'ret_in_dirt');
        if ($error !== null) return sendData(402, $error);

        return sendData(200, 'ok');
    }

    public function confirmRe(Request $request)
    {
        $params = $request->all();
        $error = $this->inConfirm($params, new SalesReturnTag(), 'ret_in_dirt',$this->user->username);
        if ($error !== null) return sendData(402, $error);
        return sendData(200, 'ok');
    }

    public function exportDoc(Request $request)
    {
        $OrderNo = $request->get('id');
        if (!$OrderNo) return sendData(402, 'ID为空！');
        $salesReturns = SalesReturn::where('OrderNo', $OrderNo)->get();
        $tpl = public_path() . '/sheets/instorage.docx';

        $template = new TemplateProcessor($tpl);

        $template->cloneRow('OrderNo', count($salesReturns));

        foreach ($salesReturns as $key => $value) {
            $i = $key + 1;
            $template->setValue('OrderNo#' . $i, $value->OrderNo);
            $template->setValue('productCd#' . $i, $value->NewProductCd . '/' . $value->ProductCd);
            $template->setValue('number#' . $i, $value->AdmQnty);
        }

        $filename = storage_path() . '/' . $OrderNo . '.docx';
        $template->saveAs($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        $parm = $request->all();
        if ($parm['id']) {
            $pieces = explode(",", $parm['id']);
            $salesOuts = SalesReturn::whereIn('OrderNo', $pieces)->with(['product', 'tag'])->get();
        } else {
            $querys = json_decode($parm['query']);
            $params = $cparams = [];
            if (isset($querys->OrderNo)) $params[] = ['OrderNo', 'like', '%' . $querys->OrderNo];
            if (isset($querys->InStcNo)) $params[] = ['InStcNo', 'like', '%' . $querys->InStcNo];
            if (isset($querys->FineFlg)) $params[] = ['FineFlg', 'like', '%' . $querys->FineFlg];
            if (isset($querys->created_at) && $querys->created_at[0] && $querys->created_at[0] != 'null') {
                $starttime = date('Y-m-d H:i:s', strtotime($querys->created_at[0]));
                $endtime = date('Y-m-d H:i:s', strtotime($querys->created_at[1]));
                $params[] = ['created_at', '>=', $starttime];
                $params[] = ['created_at', '<=', $endtime];
            }
            $salesOuts = SalesReturn::where($params)->with(['product', 'tag'])->get();
        }


        foreach ($salesOuts as $salesOut) {
            $relatedIds[] = $salesOut->id;
            $tmp = str_replace('<', '', $salesOut->Memo);
            $tmp = str_replace('(', '（', $tmp);
            $tmp = str_replace(')', '）', $tmp);
            $tmp = str_replace('>', '', $tmp);
            $memos[$salesOut->id] = $tmp;
        }
        $data = GoodsRecord::whereIn('related_id', $relatedIds)->where('type', 'ret_in_dirt')->with(['product'])->get();
        $res = [['品名', '新产品代码', '产品代码', '有效期', '出入库类型', '数量', '未入库数量', '入库中', '加工中', '加工完成', '已回传', '库位号','状态', 'SAP单号', '备注', '创建时间']];
        if (count($data) == 0) {
            foreach ($salesOuts as $key => $val) {
                $res[] = [
                    $val['product']['PRODCHINM'],
                    $val['product']['NewPRODUCTCD'],
                    $val['product']['PRODUCTCD'],
                    '',
                    'asn',
                    $val['AdmQnty'],
                    $val['AdmQnty'],
                    0,
                    0,
                    0,
                    0,
                    '',
                    '',
                    $val->OrderNo,
                    $val->Memo,
                    $val->toArray()['created_at']
                ];
            }
        }
        foreach ($data as $key => $value) {
            $odd = SalesReturn::where('id', $value['related_id'])->with(['product', 'tag'])->first();
            $query = ['state_name' => '加工完成', 'product_id' => $value->product->id,];
            $toConfirmNumber = Goods::where($query)->sum('number');
            $where = ['state_name' => 'C302', 'product_id' => $value->product->id,];
            $library = Goods::where($where)->where('stock_no', '<>', '加工区')->sum('number');
            $process = ['stock_no' => '加工区', 'state_name' => 'C302', 'product_id' => $value->product->id,];
            $process = Goods::where($process)->sum('number');
            $todoNumber = $odd['tag'] ? $value['number'] - $odd['tag']->number : $value['number'];
            if ($todoNumber < 0) {
                $todoNumber = 0;
            }
            if ($odd['tag'] && $odd['tag']->confirmNum > $value['number']) {
                $odd['tag']->confirmNum = $value['number'];
            }
            $res[] = [
                $value['product']['PRODCHINM'],
                $value['product']['NewPRODUCTCD'],
                $value['product']['PRODUCTCD'],
                $value['available_time'],
                $value['type'],
                $value['number'],
                $todoNumber,
                $library,
                $process,
                $toConfirmNumber,
                $odd['tag'] ? $odd['tag']->confirmNum : 0,
                $value['stock_no'],
                $value['state_name'],
                $odd->OrderNo,
                $memos[$value['related_id']],
                $odd->toArray()['created_at']
            ];
        }
        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->rows($res);
            });
        })->export('xls');
    }

}