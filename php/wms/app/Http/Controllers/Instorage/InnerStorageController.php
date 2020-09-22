<?php

namespace App\Http\Controllers\Instorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Instorage\InnerStorage;
use App\Models\Instorage\InnerStorageTag;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Storage\Goods;
use Excel;


class InnerStorageController extends InBaseController
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
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $params = [];
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        if ($request->get('CustomerOrderNo')) $params[] = ['CustomerOrderNo', 'like', '%' . $request->get('CustomerOrderNo')];
        if ($request->get('InvoiceNo')) $params[] = ['InvoiceNo', 'like', '%' . $request->get('InvoiceNo')];
        if ($request->get('PlaceCd')) $params[] = ['PlaceCd', 'like', '%' . $request->get('PlaceCd')];
        if ($request->get('LGORT')) $params[] = ['LGORT', 'like', '%' . $request->get('LGORT')];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['created_at', '>=', $starttime];
            $params[] = ['created_at', '<=', $endtime];
        }
        if ($request->get('time') && $request->get('time')[0] && $request->get('time')[0] != 'null') {
            $stime = date('Y-m-d H:i:s', strtotime($request->get('time')[0]));
            $etime = date('Y-m-d H:i:s', strtotime($request->get('time')[1]));
            $params[] = ['time', '>=', $stime];
            $params[] = ['time', '<=', $etime];
        }
        if ($request->get('PlaceCd')) $params[] = ['PlaceCd', 'like', '%' . $request->get('PlaceCd')];
        if ($request->get('state')) $params[] = ['state', '=', $request->get('state')];

        $data = DB::table('asn')
            ->select('CustomerOrderNo', 'InvoiceNo', 'CompanyCd', 'PlaceCd', 'LGORT', 'state','time', DB::raw('MAX(created_at) as created_at'))
            ->where($params)
            ->orderBy('CustomerOrderNo', 'desc')
            ->groupBy('CustomerOrderNo', 'InvoiceNo', 'CompanyCd', 'PlaceCd', 'LGORT', 'state','time')
            ->paginate($limit);
        foreach ($data as $key => $item) {
            $data[$key]->customer = DB::table('company')->where('CompanyCd', $item->CompanyCd)->first();
        }
        // 查询是否入库完成
        foreach ($data as $key => $item) {
            $data[$key]->is_finished = 1;
            $data[$key]->is_confirmed = 1;
            $res = InnerStorage::where('CustomerOrderNo', $item->CustomerOrderNo)->with('tag')->get();
//            $arr = InnerStorage::where('CustomerOrderNo', $item->CustomerOrderNo)->pluck('id');
//            $tag = InnerStorageTag::whereIn('related_id',$arr)->pluck('state')->toarray();
//            if(count($arr)!=count($tag)){
//                if(count($tag) == 0){
//                    $state = '未入库';
//                }else{
//                    $state = '入库中';
//                }
//            }else{
//                $merge = array_unique($tag);
//                if (count($merge)!= 1){
//                    foreach ($merge as $k=>$v){
//                        if ($v == '入库中'){
//                            $state = '入库中';
//                        }else if ($v =='已入库' ){
//                            $state = '已入库';
//                        }else{
//                            $state = '入库中';
//                        }
//                    }
//                }else{
//                    $state = $merge;
//                }
//            }
//            $data[$key]->state = $state;
            foreach ($res as $value) {
                if (!$value->tag || $value->tag->number != $value->InQnty) $data[$key]->is_finished = 0;
                if ($value->tag && $value->tag->confirmNum < $value->tag->number) $data[$key]->is_confirmed = 0;
            }
        }

        return sendData(200, '', $data);
    }

    public function stockIn(Request $request)
    {
        $params = $request->all();
        $error = $this->inStorage($params, new InnerStorageTag(), 'asn');
        if ($error !== null) return sendData(402, $error);

        return sendData(200, 'ok');
    }

    public function getById(Request $request)
    {
        $inner = InnerStorage::where('CustomerOrderNo', $request->get('id'))
            ->with(['product', 'company', 'tag'])->get();
        foreach ($inner as $key => $order) {
            if (isset($order->tag->confirmNum) && $order->tag->confirmNum == $inner[$key]->InQnty) {
                $inner[$key]->todoNumber = 0;
                $inner[$key]->query = 0;
                $inner[$key]->library = 0;
                $inner[$key]->process = 0;
                $inner[$key]->confirmNum = $inner[$key]->InQnty;
            } else {
                if ($order->tag) {
                    $query = ['product_id' => $order->product->id, 'odd' => $order->CustomerOrderNo];
                    $process = ['stock_no' => '加工区', 'state_name' => 'C302', 'product_id' => $order->product->id, 'odd' => $order->CustomerOrderNo];
                    $stock = ['origin_stock_no' => '加工区', 'product_id' => $order->product->id, 'odd' => $order->CustomerOrderNo];
//                $where = ['state_name' => 'C302', 'product_id' => $order->product->id,];
//                $library = GoodsRecord::where($where)->where('stock_no', '<>', '加工区')->where('odd', $order->InvoiceNo)->sum('number');
                    $process = GoodsRecord::where($process)->sum('number');

                    $origin = GoodsRecord::where($stock)->sum('number');
                    $toConfirmNumber = GoodsRecord::where($query)->where('type','<>','instorage_process')->where('state_name','<>','C302')->sum('number');
                    $left = $order->tag->number - $order->tag->confirmNum;
                    $inner[$key]->toConfirmNumber = $toConfirmNumber > $left ? $left : $toConfirmNumber;
                    $inner[$key]->todoNumber = $inner[$key]->InQnty - $order->tag->number;
                    $inner[$key]->query = $toConfirmNumber - $order->tag->confirmNum;
                    $inner[$key]->confirmNum = $order->tag->confirmNum;
                    $inner[$key]->process = $process - $origin;
                    $inner[$key]->library = $inner[$key]->InQnty - $inner[$key]->todoNumber - $inner[$key]->query - $order->tag->confirmNum - $inner[$key]->process;
                } else {
                    $inner[$key]->todoNumber = $inner[$key]->InQnty;
                    $inner[$key]->query = 0;
                    $inner[$key]->library = 0;
                    $inner[$key]->process = 0;
                    $inner[$key]->confirmNum = 0;
                }
            }
        }
        return sendData(200, '', $inner);
    }

    public function hasStocked(Request $request)
    {
        $id = $request->get('id');
        $product_id = $request->get('product_id');
        $params = [
            ['related_id', '=', $id],
            ['type', '=', 'asn'],
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
        $error = $this->inConfirm($params, new InnerStorageTag(), 'asn',$this->user->username);
        if ($error !== null) return sendData(402, $error);
        return sendData(200, 'ok');
    }

    public function exportDoc(Request $request)
    {
        $CustomerOrderNo = $request->get('id');
        if (!$CustomerOrderNo) return sendData(402, 'ID为空！');
        $salesReturns = InnerStorage::where('CustomerOrderNo', $CustomerOrderNo)->get();
        $tpl = public_path() . '/sheets/instorage.docx';

        $template = new TemplateProcessor($tpl);

        $template->cloneRow('OrderNo', count($salesReturns));

        foreach ($salesReturns as $key => $value) {
            $i = $key + 1;
            $template->setValue('OrderNo#' . $i, $value->CustomerOrderNo);
            $template->setValue('productCd#' . $i, $value->NewProductCD . '/' . $value->ProductCD);
            $template->setValue('number#' . $i, $value->InQnty);
        }

        $filename = storage_path() . '/' . $CustomerOrderNo . '.docx';
        $template->saveAs($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        $parm = $request->all();
        if ($parm['id']) {
            $pieces = explode(",", $parm['id']);
            $salesOuts = InnerStorage::whereIn('CustomerOrderNo', $pieces)->with(['product', 'tag'])->get();
        } else {
            $querys = json_decode($parm['query']);
            $params = $cparams = [];
            if (isset($querys->CustomerOrderNo)) $params[] = ['CustomerOrderNo', 'like', '%' . $querys->CustomerOrderNo];
            if (isset($querys->InvoiceNo)) $params[] = ['InvoiceNo', 'like', '%' . $querys->InvoiceNo];
            if (isset($querys->PlaceCd)) $params[] = ['PlaceCd', 'like', '%' . $querys->PlaceCd];
            if (isset($querys->LGORT)) $params[] = ['LGORT', 'like', '%' . $querys->LGORT];
            if (isset($querys->created_at) && $querys->created_at[0] && $querys->created_at[0] != 'null') {
                $starttime = date('Y-m-d H:i:s', strtotime($querys->created_at[0]));
                $endtime = date('Y-m-d H:i:s', strtotime($querys->created_at[1]));
                $params[] = ['created_at', '>=', $starttime];
                $params[] = ['created_at', '<=', $endtime];
            }
            $salesOuts = InnerStorage::where($params)->with(['product', 'tag'])->get();
        }

        foreach ($salesOuts as $salesOut) {
            $relatedIds[] = $salesOut->id;
            $tmp = str_replace('<', '', $salesOut->Memo);
            $tmp = str_replace('(', '（', $tmp);
            $tmp = str_replace(')', '）', $tmp);
            $tmp = str_replace('>', '', $tmp);
            $memos[$salesOut->id] = $tmp;
        }
        $data = GoodsRecord::whereIn('related_id', $relatedIds)->where('type', 'asn')->with(['product'])->get();
        $res = [['品名', '新产品代码', '产品代码', '有效期', '出入库类型', '数量', '未入库数量', '入库中', '加工中', '加工完成', '已回传', '库位号', '状态','SAP单号', '备注', '创建时间']];
        if (count($data) == 0) {
            foreach ($salesOuts as $key => $val) {
                $res[] = [
                    $val['product']['PRODCHINM'],
                    $val['product']['NewPRODUCTCD'],
                    $val['product']['PRODUCTCD'],
                    '',
                    'asn',
                    $val['InQnty'],
                    $val['InQnty'],
                    0,
                    0,
                    0,
                    0,
                    '',
                    $val->CustomerOrderNo,
                    '',
                    '',
                    $val->toArray()['created_at']
                ];
            }
        }
        foreach ($data as $key => $value) {
            $odd = InnerStorage::where('id', $value['related_id'])->with(['tag'])->first();
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
                $odd->CustomerOrderNo,
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