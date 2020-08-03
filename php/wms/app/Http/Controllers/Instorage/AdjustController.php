<?php

namespace App\Http\Controllers\Instorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Outstorage\Adjust;
use App\Models\Outstorage\AdjustTag;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\GoodsRecord;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Storage\Goods;
use Excel;


class AdjustController extends InBaseController
{
    protected $rules = [];

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->user = auth()->user();
    }

    /**
     * 分页获取移动入库指令
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = [['o.InOutFlg', '=', '0']];
        if ($request->get('AdjustNo')) $params[] = ['o.AdjustNo', 'like', '%' . $request->get('AdjustNo')];
        if ($request->get('OutStcNo')) $params[] = ['o.OutStcNo', 'like', '%' . $request->get('OutStcNo')];
        if ($request->get('FineFlg')) $params[] = ['o.FineFlg', 'like', '%' . $request->get('FineFlg')];
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

        if ($request->get('state')) $params[] = ['state', '=', $request->get('state')];
        $data = DB::table('adj_out_dirt as o')
            ->select('o.AdjustNo', 'o.OutStcNo', 'd.DeliverAdd', 'o.state', 'o.AUART', 'o.FineFlg', 'c.ShopSignNM','o.time', DB::raw('MAX(o.created_at) as created_at'))
            ->where($params)
            ->orderBy('o.OutStcNo', 'desc')
            ->groupBy('o.AdjustNo', 'o.OutStcNo', 'd.DeliverAdd', 'o.state', 'o.AUART', 'o.FineFlg', 'c.ShopSignNM','o.time')
            ->leftJoin('customer as c', 'o.CustomerCd', '=', 'c.CUSTOMERCD')
            ->leftJoin('deliver_address as d', 'o.DeliverAddCD', '=', 'd.DeliverAddCD')
            ->paginate($limit);
        // 查询是否入库完成
        foreach ($data as $key => $item) {
            $data[$key]->is_finished = 1;
            $data[$key]->is_confirmed = 1;
            $res = Adjust::where('AdjustNo', $item->AdjustNo)->with('tag')->get();
            foreach ($res as $value) {
                if (!$value->tag || $value->tag->number != $value->AdjustQnty) $data[$key]->is_finished = 0;
                if ($value->tag && $value->tag->confirmNum < $value->tag->number) $data[$key]->is_confirmed = 0;
            }
        }
        return sendData(200, '', $data);
    }

    public function getById(Request $request)
    {
        $adjusts = Adjust::where('AdjustNo', $request->get('id'))
            ->with(['product', 'company', 'tag'])->get();
        foreach ($adjusts as $key => $order) {
            if(isset($order->tag->confirmNum) && $order->tag->confirmNum == $adjusts[$key]->AdjustQnty){
                $adjusts[$key]->todoNumber = 0;
                $adjusts[$key]->query = 0;
                $adjusts[$key]->library = 0;
                $adjusts[$key]->process = 0;
                $adjusts[$key]->confirmNum = $adjusts[$key]->AdjustQnty;
            }else {
                if ($order->tag) {
                    $query = ['product_id' => $order->product->id, 'odd' => $order->AdjustNo];
                    $process = ['stock_no' => '加工区', 'state_name' => 'C302', 'product_id' => $order->product->id, 'odd' => $order->AdjustNo];
                    $stock = ['origin_stock_no' => '加工区', 'product_id' => $order->product->id, 'odd' => $order->AdjustNo];
//                $where = ['product_id' => $order->product->id,];
//                //入库中
//                $library = GoodsRecord::where($where)->where('stock_no', '<>', '加工区')->where('odd', $order->InvoiceNo)->sum('number');
//                dd($library);
                    $process = GoodsRecord::where($process)->sum('number');
                    $origin = GoodsRecord::where($stock)->sum('number');
                    $toConfirmNumber = GoodsRecord::where($query)->where('type','<>','instorage_process')->where('state_name','<>','C302')->sum('number');
                    $left = $order->tag->number - $order->tag->confirmNum;
                    $adjusts[$key]->toConfirmNumber = $toConfirmNumber > $left ? $left : $toConfirmNumber;
                    $adjusts[$key]->todoNumber = $adjusts[$key]->AdjustQnty - $order->tag->number;
                    $adjusts[$key]->query = $toConfirmNumber - $order->tag->confirmNum;
                    $adjusts[$key]->confirmNum = $order->tag->confirmNum;
                    $adjusts[$key]->process = $process - $origin;
                    $adjusts[$key]->library = $adjusts[$key]->AdjustQnty - $adjusts[$key]->todoNumber - $adjusts[$key]->query - $order->tag->confirmNum - $adjusts[$key]->process;
                } else {
                    $adjusts[$key]->todoNumber = $adjusts[$key]->AdjustQnty;
                    $adjusts[$key]->query = 0;
                    $adjusts[$key]->library = 0;
                    $adjusts[$key]->process = 0;
                    $adjusts[$key]->confirmNum = 0;
                }
            }
        }

        return sendData(200, '', $adjusts);
    }

    public function hasStocked(Request $request)
    {
        $id = $request->get('id');
        $product_id = $request->get('product_id');
        $params = [
            ['related_id', '=', $id],
            ['type', '=', 'adj_out_dirt'],
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
        $error = $this->inStorage($params, new AdjustTag(), 'adj_out_dirt');
        if ($error !== null) return sendData(402, $error);

        return sendData(200, 'ok');
    }

    public function confirmRe(Request $request)
    {
        set_time_limit(0);
        $params = $request->all();
        $error = $this->inConfirm($params, new AdjustTag(), 'adj_out_dirt',$this->user->username);
        if ($error !== null) return sendData(402, $error);
        return sendData(200, 'ok');
    }

    public function exportDoc(Request $request)
    {
        $AdjustNo = $request->get('id');
        if (!$AdjustNo) return sendData(402, 'ID为空！');
        $salesReturns = Adjust::where('AdjustNo', $AdjustNo)->get();
        $tpl = public_path() . '/sheets/instorage.docx';

        $template = new TemplateProcessor($tpl);
        $template->cloneRow('OrderNo', count($salesReturns));

        foreach ($salesReturns as $key => $value) {
            $i = $key + 1;
            $template->setValue('OrderNo#' . $i, $value->AdjustNo);
            $template->setValue('productCd#' . $i, $value->NewProductCd . '/' . $value->ProductCd);
            $template->setValue('number#' . $i, $value->AdjustQnty);
        }

        $filename = storage_path() . '/' . $AdjustNo . '.docx';
        $template->saveAs($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        $parm = $request->all();
        if ($parm['id']) {
            $pieces = explode(",", $parm['id']);
            $salesOuts = Adjust::whereIn('AdjustNo', $pieces)->with(['product', 'tag'])->get();
        } else {
            $querys = json_decode($parm['query']);
            $params = $cparams = [];
            $params = [['InOutFlg', '=', '0']];
            if (isset($querys->AdjustNo)) $params[] = ['AdjustNo', 'like', '%' . $querys->AdjustNo];
            if (isset($querys->OutStcNo)) $params[] = ['OutStcNo', 'like', '%' . $querys->OutStcNo];
            if (isset($querys->FineFlg)) $params[] = ['FineFlg', 'like', '%' . $querys->FineFlg];
            if (isset($querys->created_at) && $querys->created_at[0] && $querys->created_at[0] != 'null') {
                $starttime = date('Y-m-d H:i:s', strtotime($querys->created_at[0]));
                $endtime = date('Y-m-d H:i:s', strtotime($querys->created_at[1]));
                $params[] = ['created_at', '>=', $starttime];
                $params[] = ['created_at', '<=', $endtime];
            }
            $salesOuts = Adjust::where($params)->with(['product', 'tag'])->get();
        }

        foreach ($salesOuts as $salesOut) {
            $relatedIds[] = $salesOut->id;
            $tmp = str_replace('<', '', $salesOut->Memo);
            $tmp = str_replace('(', '（', $tmp);
            $tmp = str_replace(')', '）', $tmp);
            $tmp = str_replace('>', '', $tmp);
            $memos[$salesOut->id] = $tmp;
        }
        $data = GoodsRecord::whereIn('related_id', $relatedIds)->where('type', 'adj_out_dirt')->with(['product'])->get();
        $res = [['品名', '新产品代码', '产品代码', '有效期', '出入库类型', '数量', '未入库数量', '入库中', '加工中', '加工完成', '已回传', '库位号', '状态','SAP单号', '备注', '创建时间']];
        if (count($data) == 0) {
            foreach ($salesOuts as $key => $val) {
                $res[] = [
                    $val['product']['PRODCHINM'],
                    $val['product']['NewPRODUCTCD'],
                    $val['product']['PRODUCTCD'],
                    '',
                    'asn',
                    $val['AdjustQnty'],
                    $val['AdjustQnty'],
                    0,
                    0,
                    0,
                    0,
                    '',
                    '',
                    $val->AdjustNo,
                    $val->Memo,
                    $val->toArray()['created_at']
                ];
            }
        }
        foreach ($data as $key => $value) {
            $odd = Adjust::where('id', $value['related_id'])->with(['tag'])->first();
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
                $odd->AdjustNo,
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