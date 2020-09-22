<?php

namespace App\Http\Controllers\Instorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Instorage\MovementStorage;
use App\Models\Instorage\MovementStorageTag;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\GoodsRecord;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Storage\Goods;
use Excel;


class MovementStorageController extends InBaseController
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
        $params = [];
        if ($request->get('MoveNo')) $params[] = ['MoveNo', 'like', '%' . $request->get('MoveNo')];
        if ($request->get('InStcNo')) $params[] = ['InStcNo', 'like', '%' . $request->get('InStcNo')];
        if ($request->get('MovFrmCD')) $params[] = ['MovFrmCD', 'like', '%' . $request->get('MovFrmCD')];
        if ($request->get('EMPST')) $params[] = ['EMPST', 'like', '%' . $request->get('EMPST')];
        if ($request->get('AUART')) $params[] = ['AUART', 'like', '%' . $request->get('AUART')];
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
        if ($request->get('state')) $params[] = ['state', '=', $request->get('state')];
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $data = DB::table('move_in_dirt')
            ->select('MoveNo', 'InStcNo', 'MovToCD', 'EMPST', 'AUART', 'CompanyCD', 'state', 'MovFrmCD','time', DB::raw('MAX(created_at) as created_at'))
            ->orderBy('MoveNo', 'desc')
            ->where($params)
            ->groupBy('MoveNo', 'InStcNo', 'MovToCD', 'EMPST', 'AUART', 'CompanyCD', 'state','time', 'MovFrmCD')
            ->paginate($limit);
        foreach ($data as $key => $item) {
            $data[$key]->customer = DB::table('company')->where('CompanyCd', $item->CompanyCD)->first();
        }

        // 查询是否入库完成
        foreach ($data as $key => $item) {
            $data[$key]->is_finished = 1;
            $data[$key]->is_confirmed = 1;
            $res = MovementStorage::where('MoveNo', $item->MoveNo)->with('tag')->get();
            foreach ($res as $value) {
                if (!$value->tag || $value->tag->number != $value->MovAdmQnty) $data[$key]->is_finished = 0;
                if ($value->tag && $value->tag->confirmNum < $value->tag->number) $data[$key]->is_confirmed = 0;
            }
        }
        return sendData(200, '', $data);
    }

    public function getById(Request $request)
    {
        $movementstorage = MovementStorage::where('MoveNo', $request->get('id'))
            ->with(['product', 'company', 'tag'])->get();
        foreach ($movementstorage as $key => $order) {
            if(isset($order->tag->confirmNum) && $order->tag->confirmNum ==$movementstorage[$key]->MovAdmQnty){
                $movementstorage[$key]->todoNumber = 0;
                $movementstorage[$key]->query = 0;
                $movementstorage[$key]->library = 0;
                $movementstorage[$key]->process = 0;
                $movementstorage[$key]->confirmNum = $movementstorage[$key]->MovAdmQnty;
            }else {
                if ($order->tag) {
                    $query = ['product_id' => $order->product->id, 'odd' => $order->MoveNo];
                    $process = ['stock_no' => '加工区', 'state_name' => 'C302', 'product_id' => $order->product->id, 'odd' => $order->MoveNo];
                    $stock = ['origin_stock_no' => '加工区', 'product_id' => $order->product->id, 'odd' => $order->MoveNo];
//                $where = ['state_name' => 'C302', 'product_id' => $order->product->id,];
//                $library = GoodsRecord::where($where)->where('stock_no', '<>', '加工区')->where('odd', $order->InvoiceNo)->sum('number');
                    $process = GoodsRecord::where($process)->sum('number');

                    $origin = GoodsRecord::where($stock)->sum('number');
                    $toConfirmNumber = GoodsRecord::where($query)->where('type','<>','instorage_process')->where('state_name','<>','C302')->sum('number');
                    $left = $order->tag->number - $order->tag->confirmNum;
                    $movementstorage[$key]->toConfirmNumber = $toConfirmNumber > $left ? $left : $toConfirmNumber;
                    $movementstorage[$key]->todoNumber = $movementstorage[$key]->MovAdmQnty - $order->tag->number;
                    $movementstorage[$key]->query = $toConfirmNumber - $order->tag->confirmNum;
                    $movementstorage[$key]->confirmNum = $order->tag->confirmNum;
                    $movementstorage[$key]->process = $process - $origin;
                    $movementstorage[$key]->library = $movementstorage[$key]->MovAdmQnty - $movementstorage[$key]->todoNumber - $movementstorage[$key]->query - $order->tag->confirmNum - $movementstorage[$key]->process;
                } else {
                    $movementstorage[$key]->todoNumber = $movementstorage[$key]->MovAdmQnty;
                    $movementstorage[$key]->query = 0;
                    $movementstorage[$key]->library = 0;
                    $movementstorage[$key]->process = 0;
                    $movementstorage[$key]->confirmNum = 0;
                }
            }

        }
        return sendData(200, '', $movementstorage);
    }

    public function hasStocked(Request $request)
    {
        $id = $request->get('id');
        $product_id = $request->get('product_id');
        $params = [
            ['related_id', '=', $id],
            ['type', '=', 'move_in_dirt'],
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
        $error = $this->inStorage($params, new MovementStorageTag(), 'move_in_dirt');
        if ($error !== null) return sendData(402, $error);

        return sendData(200, 'ok');
    }

    public function confirmRe(Request $request)
    {
        set_time_limit(0);
        $params = $request->all();
        $error = $this->inConfirm($params, new MovementStorageTag(), 'move_in_dirt',$this->user->username);
        if ($error !== null) return sendData(402, $error);
        return sendData(200, 'ok');
    }

    public function exportDoc(Request $request)
    {
        $MoveNo = $request->get('id');
        if (!$MoveNo) return sendData(402, 'ID为空！');
        $salesReturns = MovementStorage::where('MoveNo', $MoveNo)->get();
        $tpl = public_path() . '/sheets/instorage.docx';

        $template = new TemplateProcessor($tpl);
        $template->cloneRow('OrderNo', count($salesReturns));

        foreach ($salesReturns as $key => $value) {
            $i = $key + 1;
            $template->setValue('OrderNo#' . $i, $value->MoveNo);
            $template->setValue('productCd#' . $i, $value->NewProductCd . '/' . $value->ProductCd);
            $template->setValue('number#' . $i, $value->MovAdmQnty);
        }

        $filename = storage_path() . '/' . $MoveNo . '.docx';
        $template->saveAs($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        $parm = $request->all();
        if ($parm['id']) {
            $pieces = explode(",", $parm['id']);
            $salesOuts = MovementStorage::whereIn('MoveNo', $pieces)->with(['product', 'tag'])->get();
        } else {
            $querys = json_decode($parm['query']);
            $params = $cparams = [];
            if (isset($querys->MoveNo)) $params[] = ['MoveNo', 'like', '%' . $querys->MoveNo];
            if (isset($querys->InStcNo)) $params[] = ['InStcNo', 'like', '%' . $querys->InStcNo];
            if (isset($querys->MovFrmCD)) $params[] = ['MovFrmCD', 'like', '%' . $querys->MovFrmCD];
            if (isset($querys->EMPST)) $params[] = ['EMPST', 'like', '%' . $querys->EMPST];
            if (isset($querys->AUART)) $params[] = ['AUART', 'like', '%' . $querys->AUART];
            if (isset($querys->created_at) && $querys->created_at[0] && $querys->created_at[0] != 'null') {
                $starttime = date('Y-m-d H:i:s', strtotime($querys->created_at[0]));
                $endtime = date('Y-m-d H:i:s', strtotime($querys->created_at[1]));
                $params[] = ['created_at', '>=', $starttime];
                $params[] = ['created_at', '<=', $endtime];
            }
            $salesOuts = MovementStorage::where($params)->with(['product', 'tag'])->get();
        }

        foreach ($salesOuts as $salesOut) {
            $relatedIds[] = $salesOut->id;
            $tmp = str_replace('<', '', $salesOut->Memo);
            $tmp = str_replace('(', '（', $tmp);
            $tmp = str_replace(')', '）', $tmp);
            $tmp = str_replace('>', '', $tmp);
            $memos[$salesOut->id] = $tmp;
        }
        $data = GoodsRecord::whereIn('related_id', $relatedIds)->where('type', 'move_in_dirt')->with(['product'])->get();
        $res = [['品名', '新产品代码', '产品代码', '有效期', '出入库类型', '数量', '未入库数量', '入库中', '加工中', '加工完成', '已回传', '库位号', '状态','SAP单号', '备注', '创建时间']];
        if (count($data) == 0) {
            foreach ($salesOuts as $key => $val) {
                $res[] = [
                    $val['product']['PRODCHINM'],
                    $val['product']['NewPRODUCTCD'],
                    $val['product']['PRODUCTCD'],
                    '',
                    'asn',
                    $val['MovAdmQnty'],
                    $val['MovAdmQnty'],
                    0,
                    0,
                    0,
                    0,
                    '',
                    '',
                    $val->MoveNo,
                    $val->Memo,
                    $val->toArray()['created_at']
                ];
            }
        }
        foreach ($data as $key => $value) {
            $odd = MovementStorage::where('id', $value['related_id'])->with(['product', 'tag'])->first();
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
                $odd->MoveNo,
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