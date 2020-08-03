<?php

namespace App\Http\Controllers\Instorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Instorage\ProcurementStorage;
use App\Models\Instorage\ProcurementStorageTag;
use App\Models\Storage\GoodsRecord;
use App\Models\Storage\Goods;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Excel;


class ProcurementStorageController extends InBaseController
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
     * 分页获取采购入库指令
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $params = [];
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        if ($request->get('InvoiceNo')) $params[] = ['InvoiceNo', 'like', '%' . $request->get('InvoiceNo')];
        if ($request->get('BSART')) $params[] = ['BSART', 'like', '%' . $request->get('BSART')];
        if ($request->get('PARTN')) $params[] = ['PARTN', 'like', '%' . $request->get('PARTN')];
        if ($request->get('LGORT')) $params[] = ['LGORT', 'like', '%' . $request->get('LGORT')];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['created_at', '>=', $starttime];
            $params[] = ['created_at', '<=', $endtime];
        }
        if ($request->get('state')) $params[] = ['state', '=', $request->get('state')];
        if ($request->get('time') && $request->get('time')[0] && $request->get('time')[0] != 'null') {
            $stime = date('Y-m-d H:i:s', strtotime($request->get('time')[0]));
            $etime = date('Y-m-d H:i:s', strtotime($request->get('time')[1]));
            $params[] = ['time', '>=', $stime];
            $params[] = ['time', '<=', $etime];
        }
        $data = DB::table('prod_imp')
            ->select('InvoiceNo', 'BSART', 'PARTN', 'LGORT', 'CompanyCD', 'state',DB::raw('MAX(time) as time'), DB::raw('MAX(created_at) as created_at'))
            ->orderBy('InvoiceNo', 'desc')
            ->where($params)
            ->groupBy('InvoiceNo', 'BSART', 'PARTN', 'LGORT', 'CompanyCD','state')
            ->paginate($limit);
        foreach ($data as $key => $item) {
            $data[$key]->customer = DB::table('company')->where('CompanyCd', $item->CompanyCD)->first();
        }
        // 查询是否入库完成
        foreach ($data as $key => $item) {
            $data[$key]->is_finished = 1;
            $data[$key]->is_confirmed = 1;
            $res = ProcurementStorage::where('InvoiceNo', $item->InvoiceNo)->with('tag')->get();
            foreach ($res as $value) {
                if (!$value->tag || $value->tag->number != $value->ImportQnty) $data[$key]->is_finished = 0;
                if ($value->tag && $value->tag->confirmNum < $value->tag->number) $data[$key]->is_confirmed = 0;
            }
        }

        return sendData(200, '', $data);
    }

    public function getById(Request $request)
    {

        $procurement = ProcurementStorage::where('InvoiceNo', $request->get('id'))
            ->with(['product', 'tag'])->get();
        foreach ($procurement as $key => $order) {
            if (isset($order->tag->confirmNum) && $order->tag->confirmNum == $procurement[$key]->ImportQnty) {
                $procurement[$key]->todoNumber = 0;
                $procurement[$key]->query = 0;
                $procurement[$key]->library = 0;
                $procurement[$key]->process = 0;
                $procurement[$key]->confirmNum = $procurement[$key]->ImportQnty;
            } else {
                if ($order->tag) {
                    $query = ['product_id' => $order->product->id, 'odd' => $order->InvoiceNo];
                    $process = ['stock_no' => '加工区', 'state_name' => 'C302', 'product_id' => $order->product->id, 'odd' => $order->InvoiceNo];
                    $stock  = ['origin_stock_no' => '加工区', 'product_id' => $order->product->id, 'odd' => $order->InvoiceNo];
                    $process = GoodsRecord::where($process)->sum('number');
                    $origin = GoodsRecord::where($stock)->sum('number');
                    $toConfirmNumber = GoodsRecord::where('state_name','<>','C302')->where('type','<>','instorage_process')->where($query)->sum('number');
                    
                    $left = $order->tag->number - $order->tag->confirmNum;
                    $procurement[$key]->toConfirmNumber = $toConfirmNumber > $left ? $left : $toConfirmNumber;
                    $procurement[$key]->todoNumber = $procurement[$key]->ImportQnty - $order->tag->number;
                    $procurement[$key]->query = $toConfirmNumber - $order->tag->confirmNum;
                    $procurement[$key]->confirmNum = $order->tag->confirmNum;
                    $procurement[$key]->process = $process - $origin;
                    $procurement[$key]->library = $procurement[$key]->ImportQnty - $procurement[$key]->todoNumber - $procurement[$key]->query - $order->tag->confirmNum - $procurement[$key]->process;
                } else {
                    $procurement[$key]->todoNumber = $procurement[$key]->ImportQnty;
                    $procurement[$key]->query = 0;
                    $procurement[$key]->library = 0;
                    $procurement[$key]->process = 0;
                    $procurement[$key]->confirmNum = 0;
                }
            }
        }

        return sendData(200, '', $procurement);
    }


    public function getByNo(Request $request)
    {
        $goods_id = DB::table('goods_tag')->where('odd',$request->get('id'))->pluck('goods_id');
        $data = DB::table('goods as g')
        ->select('g.id','g.stock_no', 'g.number', 'g.available_time', 'p.NewPRODUCTCD', 'p.PRODUCTCD', 'p.PRODCHINM','g.product_id','s.stock_state_id')
        ->where('g.state_name','加工完成')
        ->whereIn('g.id',$goods_id)
        ->groupBy('g.id','g.stock_no', 'g.number', 'g.available_time', 'p.NewPRODUCTCD', 'p.PRODUCTCD', 'p.PRODCHINM','g.product_id','s.stock_state_id')
        ->leftJoin('product as p', 'g.product_id', '=', 'p.id')
        ->leftJoin('stock as s', 'g.stock_no', '=', 's.stock_no')
        ->get();
        foreach($data as $k=>$key){
            if($key->stock_state_id){
                $stock_state = DB::table('stock_state')->where('id',$key->stock_state_id)->first();
                $data[$k]->stock_state = $stock_state->state_name;
            }else{
                $data[$k]->stock_state = '';
            }
        }
        return sendData(200,'',$data);
    }

    public function hasStocked(Request $request)
    {
        $id = $request->get('id');
        $product_id = $request->get('product_id');
        $params = [
            ['related_id', '=', $id],
            ['type', '=', 'prod_imp'],
            ['product_id', '=', $product_id],
        ];
        $goods = GoodsRecord::where($params)
            ->orderBy('CHARG', 'asc')
            ->with(['product'])
            ->get();

        return sendData(200, '', $goods);
    }

    public function confirmRe(Request $request)
    {
        $params = $request->all();
        $error = $this->inConfirm($params, new ProcurementStorageTag(), 'prod_imp',$this->user->username);
        if ($error !== null) return sendData(402, $error);
        return sendData(200, 'ok');
    }

    public function stockIn(Request $request)
    {
        $params = $request->all();
        $error = $this->inStorage($params, new ProcurementStorageTag(), 'prod_imp');
        if ($error !== null) return sendData(402, $error);

        return sendData(200, 'ok');
    }

    public function exportDoc(Request $request)
    {
        $InvoiceNo = $request->get('id');
        if (!$InvoiceNo) return sendData(402, 'ID为空！');
        $salesReturns = ProcurementStorage::where('InvoiceNo', $InvoiceNo)->get();
        $tpl = public_path() . '/sheets/instorage.docx';

        $template = new TemplateProcessor($tpl);

        $template->cloneRow('OrderNo', count($salesReturns));

        foreach ($salesReturns as $key => $value) {
            $i = $key + 1;
            $template->setValue('OrderNo#' . $i, $value->InvoiceNo);
            $template->setValue('productCd#' . $i, $value->NewProductCd . '/' . $value->ProductCd);
            $template->setValue('number#' . $i, $value->ImportQnty);
        }

        $filename = storage_path() . '/' . $InvoiceNo . '.docx';
        $template->saveAs($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        $parm = $request->all();
        if ($parm['id']) {
            $pieces = explode(",", $parm['id']);
            $salesOuts = ProcurementStorage::whereIn('InvoiceNo', $pieces)->with(['product', 'tag'])->get();
        } else {
            $querys = json_decode($parm['query']);
            $params = $cparams = [];
            if (isset($querys->InvoiceNo)) $params[] = ['InvoiceNo', 'like', '%' . $querys->InvoiceNo];
            if (isset($querys->BSART)) $params[] = ['BSART', 'like', '%' . $querys->BSART];
            if (isset($querys->PARTN)) $params[] = ['PARTN', 'like', '%' . $querys->PARTN];
            if (isset($querys->LGORT)) $params[] = ['LGORT', 'like', '%' . $querys->LGORT];
            if (isset($querys->created_at) && $querys->created_at[0] && $querys->created_at[0] != 'null') {
                $starttime = date('Y-m-d H:i:s', strtotime($querys->created_at[0]));
                $endtime = date('Y-m-d H:i:s', strtotime($querys->created_at[1]));
                $params[] = ['created_at', '>=', $starttime];
                $params[] = ['created_at', '<=', $endtime];
            }
            $salesOuts = ProcurementStorage::where($params)->with(['product', 'tag'])->get();
        }

        foreach ($salesOuts as $salesOut) {
            $relatedIds[] = $salesOut->id;
            $tmp = str_replace('<', '', $salesOut->Memo);
            $tmp = str_replace('(', '（', $tmp);
            $tmp = str_replace(')', '）', $tmp);
            $tmp = str_replace('>', '', $tmp);
            $memos[$salesOut->id] = $tmp;
        }
        $data = GoodsRecord::whereIn('related_id', $relatedIds)->where('type', 'prod_imp')->with(['product'])->get();
        $res = [['品名', '新产品代码', '产品代码', '有效期', '出入库类型', '数量', '未入库数量', '入库中', '加工中', '加工完成', '已回传', '库位号','状态', 'SAP单号', '备注', '创建时间']];
        if (count($data) == 0) {
            foreach ($salesOuts as $key => $val) {
                $res[] = [
                    $val['product']['PRODCHINM'],
                    $val['product']['NewPRODUCTCD'],
                    $val['product']['PRODUCTCD'],
                    '',
                    'asn',
                    $val['ImportQnty'],
                    $val['ImportQnty'],
                    0,
                    0,
                    0,
                    0,
                    '',
                    '',
                    $val->InvoiceNo,
                    '',
                    $val->toArray()['created_at']
                ];
            }
        }
        foreach ($data as $key => $value) {
            $odd = ProcurementStorage::where('id', $value['related_id'])->with(['product', 'tag'])->first();
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
                $odd->InvoiceNo,
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
