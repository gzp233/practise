<?php

namespace App\Http\Controllers\Outstorage;

use App\Models\Outstorage\AdjustTag;
use App\Models\Base\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use App\Models\Outstorage\Adjust;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Outstorage\OrderOutEnsure;
use App\Utils\PDF;
use App\Utils\Accept;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class AdjustController extends OutBaseController
{
    protected $rules = [];

    /**
     * Create a new SalesOutController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->user = auth()->user();
    }

    /**
     * 分页获取受注出库指令
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = [['o.InOutFlg', '=', '1']];
        if ($request->get('AdjustNo')) $params[] = ['o.AdjustNo', 'like', '%' . $request->get('AdjustNo')];
        if ($request->get('OutStcNo')) $params[] = ['o.OutStcNo', 'like', '%' . $request->get('OutStcNo')];
        if ($request->get('FineFlg')) $params[] = ['o.FineFlg', 'like', '%' . $request->get('FineFlg')];
        if ($request->get('zt')) $params[] = ['o.zt', 'like', '%' . $request->get('zt')];
//        if ($request->get('print_status')) $params[] = ['o.print_status', 'like', '%' . $request->get('print_status')];
        if ($request->get('status') && in_array($request->get('status'), ['复核中', '待发运', '发货完成', '已回传', '拣货中', '待回传'])) {
            $params[] = ['t.status', '=', $request->get('status')];
        }
        if ($request->get('print_status') && in_array($request->get('print_status'), ['已打印', '未打印'])) {
            $params[] = ['o.print_status', '=', $request->get('print_status')];
        }
        if ($request->get('ShopSignNM')) $params[] = ['c.ShopSignNM', 'like', '%' . $request->get('ShopSignNM') . '%'];
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


        $builder = DB::table('adj_out_dirt as o')
            ->select(
                'o.AdjustNo',
                'o.OutStcNo',
                'o.AUART',
                'o.FineFlg',
                't.status',
                'c.ShopSignNM',
                'o.zt',
                'o.print_status',
                'o.time',
                DB::raw('MAX(o.created_at) as created_at'),
                DB::raw('count(o.id) as type'),
                DB::raw('sum(o.AdjustQnty) as number')
            )
            ->where($params)
            ->orderBy('created_at', 'desc')
            ->groupBy('o.AdjustNo', 'o.OutStcNo', 'o.AUART', 'o.FineFlg', 'c.ShopSignNM', 't.status','o.time','o.print_status','o.zt')
            ->leftJoin('customer as c', 'o.CustomerCd', '=', 'c.CUSTOMERCD')
            ->leftJoin('adj_out_dirt_tag as t', 't.related_id', '=', 'o.id');
        if ($request->get('status') && $request->get('status') == '未处理') {
            $builder = $builder->whereNull('t.status');
        }
        $data = $builder->paginate($limit);
        foreach ($data as $key => $value) {
            $data[$key]->is_finished = 0;
            $data[$key]->pass = 0;
            if ($value->status == '已回传') {
                $data[$key]->is_finished = 1;
                $data[$key]->pass = 1;
            } elseif ($value->status == '发货完成') {
                $data[$key]->is_finished = 1;
            } elseif (!$value->status) {
                $value->status = '未处理';
            }
        }

        return sendData(200, '', $data);
    }

    public function getByNo(Request $request)
    {
        $outStcNo = $request->get('id');
        if (!$outStcNo) {
            return sendData(402, '出库单号为空');
        }
        $orders = Adjust::where('OutStcNo', $outStcNo)->with(['product', 'customer', 'deliver', 'tag'])->get();
        if (count($orders) == 0) return sendData(402, '获取失败');
        foreach ($orders as $key => $order) {
            if ($order->tag) {
                $orders[$key]->stocks = $this->getAllocated($order);
            } else {
                $res = $this->allocate($order);
                $orders[$key]->stocks = $res['stocks'];
                $orders[$key]->total = $res['total'];
                $orders[$key]->frozen = $res['frozen'];
            }
        }

        return sendData(200, '', $orders);
    }

    public function getBySee(Request $request)
    {
        $outStcNo = $request->get('id');
        if (!$outStcNo) {
            return sendData(402, '出库单号为空');
        }
        $orders = Adjust::where('OutStcNo', $outStcNo)->with(['product', 'customer', 'deliver', 'tag', 'company'])->get();
        if (count($orders) == 0) return sendData(402, '获取失败');
        return sendData(200, '', $orders);
    }

    // 自动分配库位
    private function allocate($order)
    {
        $total = $order->AdjustQnty;
        $stockedNumber = $frozenNumber = 0;
        $product = Product::where('PRODUCTCD', $order->product->PRODUCTCD)->get();
        $id = [];
        foreach ($product as $item) {
            $id[] = $item['id'];
        }
        $params = [
            ['state_name', '=', $order->FineFlg],
        ];
        $goods = Goods::where($params)
            ->whereIn('product_id',$id)
            ->whereHas('location', function ($q) {
                $q->where('stock_no', '<>', '复核区');
                $q->where('stock_no', '<>', '加工区');
                $q->where('stock_no', '<>', '移库区');
                $q->where('status', '=', '0');
            })
            ->orderBy('available_time', 'asc')
            ->orderBy('CHARG', 'asc')
            ->orderBy('number', 'asc')
            ->with(['product', 'location'])
            ->get();
        $result = [];
        foreach ($goods as $key => $item) {
            $stockedNumber += $item->number;
            $frozenNumber += $item->frozen_number;
            if ($item->number > $total) {
                $goods[$key]->actNumber = $total;
                $total = 0;
            } else {
                $goods[$key]->actNumber = $item->number;
                $total -= $item->number;
            }
            $result[] = $goods[$key];
        }

        return ['stocks' => $result, 'total' => $stockedNumber, 'frozen' => $frozenNumber];
    }

    // 获取已出库的
    private function getAllocated($order)
    {
        $product = Product::where('PRODUCTCD', $order->product->PRODUCTCD)->get();
        $id = [];
        foreach ($product as $item) {
            $id[] = $item['id'];
        }
        $params = [
            ['related_id', '=', $order->id],
            ['state_name', '=', $order->FineFlg],
            ['type', '=', 'adj_out_dirt']
        ];
        $goods = GoodsRecord::where($params)
            ->whereIn('product_id',$id)
            ->orderBy('CHARG', 'asc')
            ->with(['product'])
            ->get();

        return $goods;
    }

    public function getStockById(Request $request)
    {
        $product_id = $request->get('product_id');
        $warehouse_id = $request->get('warehouse_id');
        if (!$product_id || !$warehouse_id) return sendData(402, 'ID不能为空！');
        $data = Goods::where('product_id', $product_id)->with(['product'])->get();

        return sendData(200, '', $data);
    }

    public function stockOut(Request $request)
    {
        $all = $request->all();

        if ($all['status'] == '复核中') {
            foreach ($all['orders'] as $key => $val) {
                $list = AdjustTag::where('related_id', $val['id'])->get();
                if ($val['tag']['status'] != $list[0]['status']) {
                    return sendData(402, '该单已复核！');
                }
            }
        }
        if ($all['status'] == '拣货中') {
            foreach ($all['orders'] as $key => $val) {
                $list = AdjustTag::where('related_id', $val['id'])->get();
                if ($val['tag']['status'] != $list[0]['status']) {
                    return sendData(402, '该单已拣货！');
                }
            }
        }

        // if ($all['status'] == '待回传') {
        //     $username = $this->user->username;
        //     $name = date('Y-m-d');
        //     Log::useFiles(storage_path('logs/Outreturn/error' . $name . '.log'));
        //     //记录日志
        //     Log::info('操作人：' . $username . '出库类型：adj_out_dirt' . '出库单号：' . $all['orders'][0]['OutStcNo']);
        //     $this->adjustNo($all['orders'][0]['OutStcNo']);
        //     return sendData();
        // }
        $error = $this->outStorage($all, new AdjustTag, 'adj_out_dirt', $all['orders'][0]['AdjustNo']);
        if ($all['status'] == '拣货中') {
            DB::table('ganher')->where('OutStcNo', $all['orders'][0]['OutStcNo'])->update(['status' => 'PC']);
        }
        if ($error !== null) return sendData(402, $error);
        return sendData();
    }

    public function backNo(Request $request)
    {
        $OutStcNo = $request->get('OutStcNo');
        $arr = Adjust::where('OutStcNo',$OutStcNo)->first();
        $time = time();
        if(!$arr){
            return sendData(402, '该单号不存在');
        }
        if($arr->zt == '已回传'){
            return sendData(402, '该单号已回传');
        }
        $username = $this->user->username;
        $name = date('Y-m-d');
        Log::useFiles(storage_path('logs/Outreturn/error' . $name . '.log'));
        //记录日志
        Log::info('操作人：' . $username . '出库类型：adj_out_dirt' . '出库单号：' . $OutStcNo);
        $this->adjustNo($OutStcNo,$time);
        return sendData();
    }

    public function exportDoc(Request $request)
    {
        $OutStcNo = $request->get('id');
        if (!$OutStcNo) return sendData(402, 'ID为空！');
        DNS1D::getBarcodePNGPath($OutStcNo, "C39");
        $customer = Adjust::where('AdjustNo', $OutStcNo)->with(['customer'])->get();
        $salesOuts = DB::table('goods_record as g')
            ->leftJoin('product as p', 'g.product_id', '=', 'p.id')
            ->select('p.PRODCHINM', 'p.NewPRODUCTCD', 'p.PRODUCTCD', 'p.is_need_code', 'g.available_time', 'g.number', 'g.origin_stock_no')
            ->where('odd', $OutStcNo)
            ->orderBy('p.PRODUCTCD', 'asc')
            ->get();
        if (count($customer) == '0') {
            return sendData(402, '交货单号为空！');
        }
        if (count($salesOuts) == '0') {
            return sendData(402, '数据为空！');
        }
        $time = date('Y-m-d');
        $pre = $prf = [];
        $pre_key = '';
        foreach ($salesOuts as $k => $info) {
            $key = $info->PRODUCTCD . '-' . $info->available_time . '-' . $info->origin_stock_no;
            if ($k === 0) {
                $pre[$key] = $info;
                $pre_key = $key;
                continue;
            }
            if (array_key_exists($key, $pre)) {
                $pre[$key]->number += $info->number;
            } else {
                $prf[] = $pre[$pre_key];
                $pre = [];
                $pre_key = $key;
                $pre[$key] = $info;
            }
        }
        // 最后一条特殊处理
        $prf[] = $pre[$pre_key];
        $res = [['拣货单号:', '', '', '发票号:',  $OutStcNo, '', '交货单号:', $customer[0]->OutStcNo], '', ['客户名: ' . $customer[0]->customer->ShopSignNM, '', '', '客户号:', $customer[0]->CustomerCd, '', '拣货日期:', $time], ['拣货明细:'], ['品名', '新产品代码', '商品代码', '防串货', '有效期', '单位', '数量', '余量', '箱数', '库位号', '备注']];
        foreach ($prf as $key => $value) {
            $res[] = [
                $value->PRODCHINM,
                $value->NewPRODUCTCD,
                $value->PRODUCTCD,
                $value->is_need_code,
                $value->available_time,
                '支',
                $value->number,
                '',
                '',
                $value->origin_stock_no,
                $customer[0]->Memo,
            ];
        }
        $sum = [];
        foreach ($prf as $k => $v) {
            $sum[] = $v->number;
        }
        $res[] = [
            '合计',
            '',
            '',
            '',
            '',
            '',
            array_sum($sum),
        ];
        $res[] = [''];
        $res[] = [''];
        $res[] = [''];
        $res[] = [''];
        $res[] = [''];
        $res[] = [
            '',
            '拣货开始时间:',
            '',
            '',
            '拣货结束时间:',
        ];
        $res[] = [''];
        $res[] = [
            '',
            '拣货员签字:',
            '',
            '',
            '复核员签字:',
        ];
        $res[] = [''];
        $res[] = [
            '',
            '仓库主管签字:',
        ];
        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->getStyle('A')->getAlignment()->setShrinkToFit(true);
                $sheet->getStyle('A')->getAlignment()->setWrapText(true);
                $sheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path() . '/' . $res[0][4] . '.png');
                $objDrawing->setHeight(400);
                $objDrawing->setWidth(300);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setOffsetX(100); //写入图片在指定格中的X坐标值
                $objDrawing->setOffsetY(-25); //写入图片在指定格中的Y坐标值
                $objDrawing->setRotation(1); //设置旋转角度
                $objDrawing->getShadow()->setVisible(true); //
                $objDrawing->getShadow()->setDirection(50); //
                $objDrawing->setWorksheet($sheet);
                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),
                    ),
                );

                $styleThinBlackBorderOutlines = array(
                    'borders' => array(
                        'outline' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN, //设置border样式 'color' => array ('argb' => 'FF000000'), //设置border颜色
                        ),
                    ),
                );
                $sheet->rows($res);
                // $sheet->mergeCells('A5:C5');
                $sheet->mergeCells('A3:B3');
                $sheet->mergeCells('B6:K6');
                $sheet->mergeCells('H5:I5');
                $sheet->mergeCells('H3:I3');
                $sheet->mergeCells('E5:F5');
                $sheet->mergeCells('E3:F3');
                $sheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $G = count($res) - 8;
                $sheet->getStyle('A6:' . 'K' . $G)->applyFromArray($styleThinBlackBorderOutline);
                $W = count($res) + 2;
                $k = count($res) - 2;
                $J = count($res) - 2;
                $sheet->mergeCells('E' . count($res) . ':' . 'G' . count($res));
                $sheet->mergeCells('B' . count($res) . ':' . 'D' . count($res));
                $sheet->getStyle('D3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D5')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('G5')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $sheet->mergeCells('E' . $J . ':' . 'G' . $J);
                $sheet->mergeCells('B' . $J . ':' . 'D' . $J);
                $sheet->mergeCells('B' . $W . ':' . 'D' . $W);
                $sheet->getStyle('B' . $k . ':' . 'H' . $W)->applyFromArray($styleThinBlackBorderOutlines);
            });
        })->export('xls');
    }

    public function exportPDF(Request $request)
    {
        $OutStcNo = $request->get('id');
        if (!$OutStcNo) return sendData(402, 'ID为空！');
        $salesOuts = Adjust::where('OutStcNo', $OutStcNo)->with(['company', 'deliver', 'customer', 'product'])->get();
        $pdf = new PDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 设置文档信息
        $pdf->SetCreator('交货单');
        $pdf->SetAuthor('wms');
        $pdf->SetTitle('交货单');
        $pdf->SetSubject('交货单');
        $pdf->SetKeywords('交货单');

        // 设置默认等宽字体
        $pdf->SetDefaultMonospacedFont('courier');

        // 设置间距
        $pdf->SetMargins(14, 14, 10); //页面间隔
        $pdf->setCellMargins(0, 3, 6, 3);
        $pdf->setCellPaddings(1, 2, 1, 2);
        $pdf->SetFooterMargin(10); //页脚bottom间隔
        $pdf->setCellHeightRatio(2);

        // 设置分页
        $pdf->SetAutoPageBreak(false, 1);

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        // 设置footer条码
        $pdf->POcode = $salesOuts[0]->ApplyNo;

        //设置字体 stsongstdlight支持中文
        $pdf->SetFont('stsongstdlight', '', 9);

        //第一页
        $pdf->AddPage();
        $txt = "收货方： \n" . $salesOuts[0]->CustomerCD . '/' . $salesOuts[0]->customer->ShopSignNM;
        $pdf->MultiCell(85, 15, $txt, 1, 'L', 0, 0, '', '', true);

        $txt = "发货方： \n" . '资生堂(中国)投资有限公司)_成品';
        $pdf->MultiCell(105, 15, $txt, 1, 'L', 0, 1, '', '', true);

        $image_file = public_path() . '/image/logo_table.png';
        $pdf->Image($image_file, 212, 17, 75, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        $pdf->Ln(15);
        $pdf->setCellHeightRatio(1.25);
        $txt = "地址：" . $salesOuts[0]->deliver->DeliverAdd
            . "\n邮编：" . $salesOuts[0]->deliver->POSTCD
            . "\n电话：" . $salesOuts[0]->deliver->TELNO
            . "\nFAX：" . $salesOuts[0]->deliver->FAXNO
            . "\n\n订单原因："
            . "\n\n特殊送货地址：" . $salesOuts[0]->DeliverAddMemo
            . "\n备注：" . $salesOuts[0]->Memo
            . "\nPO编号：" . $salesOuts[0]->ApplyNo;
        $pdf->MultiCell(85, 40, $txt, 0, 'L', 0, 0, '', '', true);

        $txt = "地址： " . $salesOuts[0]->deliver->DeliverAdd
            . "\n邮编：200120\n电话：38612828\nFAX：58761109\n" .
            "发货仓库：7858_\n库位：良品\n";
        $pdf->MultiCell(105, 28, $txt, 0, 'L', 0, 1, '', '', true);

        $image_file = public_path() . '/image/logo_black.png';
        $pdf->Image($image_file, 144, 42, 67, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $txt = "单据日期：" . date('Y-m-d', strtotime($salesOuts[0]->created_at)) . "\n单据号码：" . $salesOuts[0]->AdjustNo;
        $pdf->MultiCell(105, 12, $txt, 1, 'L', 0, 1, 105, 60, true);

        // 输出具体的商品内容
        $data = [];
        $count = 1;
        $totalNum = 0;
        $totalWHOLEMNY = 0;
        $totalUNITMNY = 0;
        $totalRISETAXMNY = 0;
        foreach ($salesOuts as $salesOut) {
            $temp = [];
            $temp[] = $count;
            $temp[] = $salesOut->NewProductCd;
            $temp[] = $salesOut->ProductCd;
            $temp[] = $salesOut->product->PRODCHINM;
            $temp[] = $salesOut->AdjustQnty;
            $temp[] = "0.00";
            $temp[] = "0.00";
            $temp[] = "0.00";
            $temp[] = "0.00";
            $temp[] = "0.00";
            $temp[] = "0.00";
            $data[] = $temp;
            $count++;
            $totalNum += $salesOut->AdjustQnty;
            $totalWHOLEMNY += $salesOut->WHOLEMNY;
            $totalUNITMNY += $salesOut->UNITMNY;
            $totalRISETAXMNY += $salesOut->RISETAXMNY;
        }
        $data[] = ['', '', '', '合计', $totalNum, '', '', $totalWHOLEMNY, '', $totalUNITMNY, $totalRISETAXMNY];
        $pdf->getTable($data);
        $filename = storage_path() . '/' . $OutStcNo . '.pdf';
        //输出PDF
        $pdf->Output($filename, 'I');
    }

    public function accept(Request $request)
    {
        $OutStcNo = $request->get('id');
        if (!$OutStcNo) return sendData(402, 'ID为空！');
        $salesOuts = Adjust::where('OutStcNo', $OutStcNo)->with(['company', 'deliver', 'customer', 'product'])->get();

        $pdf = new Accept("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 设置文档信息
        $pdf->SetCreator('验收单');
        $pdf->SetAuthor('wms');
        $pdf->SetTitle('验收单');
        $pdf->SetSubject('验收单');
        $pdf->SetKeywords('验收单');

        // 设置默认等宽字体
        $pdf->SetDefaultMonospacedFont('courier');

        // 设置间距
        $pdf->SetMargins(14, 14, 10); //页面间隔
        $pdf->setCellMargins(0, 3, 6, 3);
        $pdf->setCellPaddings(1, 2, 1, 2);
        $pdf->SetFooterMargin(10); //页脚bottom间隔
        $pdf->setCellHeightRatio(2);

        // 设置分页
        $pdf->SetAutoPageBreak(false, 1);

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        // 设置footer条码
        $pdf->POcode = $salesOuts[0]->ApplyNo;

        //设置字体 stsongstdlight支持中文
        $pdf->SetFont('stsongstdlight', '', 9);

        //第一页
        $pdf->AddPage();
        $txt = "收货方： \n" . $salesOuts[0]->CustomerCD . '/' . $salesOuts[0]->customer->ShopSignNM;
        $pdf->MultiCell(85, 15, $txt, 1, 'L', 0, 0, '', '', true);

        $txt = "发货方： \n" . '资生堂(中国)投资有限公司)_成品';
        $pdf->MultiCell(105, 15, $txt, 1, 'L', 0, 1, '', '', true);

        $image_file = public_path() . '/image/logo_table.png';
        $pdf->Image($image_file, 212, 17, 75, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        $pdf->Ln(15);
        $pdf->setCellHeightRatio(1.25);

        $txt = "地址：" . $salesOuts[0]->deliver->DeliverAdd
            . "\n邮编：" . $salesOuts[0]->deliver->POSTCD
            . "\n电话：" . $salesOuts[0]->deliver->TELNO
            . "\nFAX：" . $salesOuts[0]->deliver->FAXNO
            . "\n\n订单原因："
            . "\n\n特殊送货地址：" . $salesOuts[0]->DeliverAddMemo
            . "\n备注：" . $salesOuts[0]->Memo
            . "\nPO编号：" . $salesOuts[0]->ApplyNo;
        $pdf->MultiCell(85, 40, $txt, 0, 'L', 0, 0, '', '', true);

        $txt = "地址： " . $salesOuts[0]->deliver->DeliverAdd
            . "\n邮编：200120\n电话：38612828\nFAX：58761109\n" .
            "发货仓库：7858_\n库位：良品\n";
        $pdf->MultiCell(105, 28, $txt, 0, 'L', 0, 1, '', '', true);

        $image_file = public_path() . '/image/logo_accept.png';
        $pdf->Image($image_file, 164, 42, 45, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        $txt = "单据日期：" . date('Y-m-d', strtotime($salesOuts[0]->created_at)) . "\n单据号码：" . $salesOuts[0]->AdjustNo;
        $pdf->MultiCell(105, 12, $txt, 1, 'L', 0, 1, 105, 60, true);

        // 输出具体的商品内容
        $data = [];
        $count = 1;
        $totalNum = 0;
        $totalWHOLEMNY = 0;
        $totalRISETAXMNY = 0;
        foreach ($salesOuts as $salesOut) {
            $temp = [];
            $temp[] = $count;
            $temp[] = $salesOut->NewProductCd;
            $temp[] = $salesOut->ProductCd;
            $temp[] = $salesOut->product->PRODCHINM;
            $temp[] = $salesOut->AdjustQnty;
            $temp[] = $salesOut->MailinnoBigQnty;
            $data[] = $temp;
            $count++;
            $totalNum += $salesOut->AdjustQnty;
            $totalRISETAXMNY += $salesOut->MailinnoBigQnty;
        }
        $data[] = ['', '', '', '合计', $totalNum, $totalWHOLEMNY];
        $pdf->getTable($data);
        $filename = storage_path() . '/' . $OutStcNo . '.pdf';
        //输出PDF
        $pdf->Output($filename, 'I');
    }

    public function rollback(Request $request)
    {
        $outStcNo = $request->all();
        $ganher = DB::table('ganher')->where('vbeln', $outStcNo['orders'][0]['AdjustNo'])->get();
        try {
            DB::beginTransaction();
            if (count($ganher) != 0) {
                if ($ganher[0]->status == '未集货' || $ganher[0]->status == 'PC' || $ganher[0]->status == 'App') {
                    foreach ($outStcNo as $key => $val) {
                        foreach ($val as $item) {
                            foreach ($item['stocks'] as $k => $v) {
                                $goodsData = [
                                    'product_id' => $v['product']['id'],
                                    'stock_no' => $v['origin_stock_no'],
                                    'state_name' => $v['state_name'],
                                    'CHARG' => $v['CHARG'],
                                    'available_time' => $v['available_time'],
                                ];
                                $goodsS = Goods::where($goodsData)->first();
                                if ($goodsS) {
                                    $goodsS->number += $v['number'];
                                    $goodsS->save();
                                } else {
                                    $goodsData['number'] = $v['number'];
                                    $goodsData['created_at'] = $goodsData['updated_at'] = date('Y-m-d H:i:s');
                                    Goods::insert($goodsData);
                                }
                                $where = [
                                    'product_id' => $v['product_id'],
                                    'stock_no' => $v['origin_stock_no'],
                                    'available_time' => $v['available_time'],
                                    'odd' => $v['odd'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                                DB::table('goods_rollback')->insert($where);
                                $del = [
                                    'product_id' => $v['product_id'],
                                    'number' => $v['number'],
                                    'available_time' => $v['available_time'],
                                    'stock_no' => '复核区',
                                    'state_name' => $v['state_name'],
                                    'odd' => $v['odd'],
                                ];
                                DB::table('goods_record')->where($del)->delete();
                            }
                            $goods_id = DB::table('goods_tag')->where('odd', $item['AdjustNo'])->pluck('goods_id')->toArray();
                            Goods::whereIn('id', $goods_id)->delete();
                            DB::table('goods_tag')->where('odd', $item['AdjustNo'])->delete();
                            $id = [
                                'id' => $item['tag']['id'],
                            ];
                            DB::table('adj_out_dirt_tag')->where($id)->delete();
                        }
                    }
                    DB::table('ganher')->where('vbeln', $outStcNo['orders'][0]['AdjustNo'])->delete();
                    Redis::del('jianhuo::' . $outStcNo['orders'][0]['AdjustNo']);
                    DB::table('out_record')->where('odd', $outStcNo['orders'][0]['AdjustNo'])->update(['deleted_at' => date('Y-m-d H:i:s')]);
                } else {
                    return sendData(402, '该单已集货不能回退');
                }
            } else {
                foreach ($outStcNo as $key => $val) {
                    foreach ($val as $item) {
                        foreach ($item['stocks'] as $k => $v) {
                            $goodsData = [
                                'product_id' => $v['product']['id'],
                                'stock_no' => $v['origin_stock_no'],
                                'state_name' => $v['state_name'],
                                'CHARG' => $v['CHARG'],
                                'available_time' => $v['available_time'],
                            ];
                            $goodsS = Goods::where($goodsData)->first();
                            if ($goodsS) {
                                $goodsS->number += $v['number'];
                                $goodsS->save();
                            } else {
                                $goodsData['number'] = $v['number'];
                                $goodsData['created_at'] = $goodsData['updated_at'] = date('Y-m-d H:i:s');
                                Goods::insert($goodsData);
                            }
                            $where = [
                                'product_id' => $v['product_id'],
                                'stock_no' => $v['origin_stock_no'],
                                'available_time' => $v['available_time'],
                                'odd' => $v['odd'],
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                            DB::table('goods_rollback')->insert($where);
                            $del = [
                                'product_id' => $v['product_id'],
                                'number' => $v['number'],
                                'available_time' => $v['available_time'],
                                'stock_no' => '复核区',
                                'state_name' => $v['state_name'],
                                'odd' => $v['odd'],
                            ];
                            DB::table('goods_record')->where($del)->delete();
                        }
                        $goods_id = DB::table('goods_tag')->where('odd', $item['AdjustNo'])->pluck('goods_id')->toArray();
                        Goods::whereIn('id', $goods_id)->delete();
                        DB::table('goods_tag')->where('odd', $item['AdjustNo'])->delete();
                        $id = [
                            'id' => $item['tag']['id'],
                        ];
                        DB::table('adj_out_dirt_tag')->where($id)->delete();
                    }
                }
                DB::table('ganher')->where('vbeln', $outStcNo['orders'][0]['AdjustNo'])->delete();
                Redis::del('jianhuo::' . $outStcNo['orders'][0]['AdjustNo']);
                DB::table('out_record')->where('odd', $outStcNo['orders'][0]['AdjustNo'])->update(['deleted_at' => date('Y-m-d H:i:s')]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }

    public function wave(Request $request)
    {
        $OutStcNo = $request->all();
        $pieces = explode(",", $OutStcNo['id']);
        $salesOuts = Adjust::whereIn('AdjustNo', $pieces)->with(['customer', 'tag'])->get();
        $tpl = public_path() . '/sheets/gather.docx';
        $template = new TemplateProcessor($tpl);
        $total = 0;
        $template->setValue('date', date('Y-m-d'));
        $relatedIds = $memos = [];
        foreach ($salesOuts as $salesOut) {
            if (!$salesOut->tag) return sendData(402, '订单未拣货！');
            $total += $salesOut->tag->number;
            $relatedIds[] = $salesOut->id;
            $tmp = str_replace('<', '', $salesOut->Memo);
            $tmp = str_replace('(', '（', $tmp);
            $tmp = str_replace(')', '）', $tmp);
            $tmp = str_replace('>', '', $tmp);
            $memos[$salesOut->id] = $tmp;
        }
        $template->setValue('total', $total);
        $records = GoodsRecord::where('type', 'adj_out_dirt')
            ->whereIn('related_id', $relatedIds)
            ->orderBy('origin_stock_no', 'asc')
            ->with(['product'])
            ->get();
        foreach ($records as $v) {
            $OrderNo = Adjust::where('id', $v->related_id)->get();
            $where = [
                'product_id' => $v->product_id,
                'unit_name' => "箱"
            ];
            $unit = DB::table('unit')->where($where)->get();
            if (count($unit) == 0) {
                $rule = '0';
            } else {
                $rule = $unit[0]->number;
            }
            if (count($unit) == 0 || $unit[0]->number > $v->number) {
                $box = 0;
                $surplus = $v->number;
            } else {
                $surplus = $v->number % $unit[0]->number;
                $box = ($v->number - $surplus) / $unit[0]->number;
            }
            $res = [
                "productId" => $v->product_id,
                "productName" => $v->product->PRODCHINM,
                "type" => $v->product->PRODUCTCD,
                "box" => $box,
                "rule" => $rule,
                "surplus" => $surplus,
                "orderNo" => $OrderNo[0]->AdjustNo,
                "Valid" => $v->available_time,
                "stock" => $v->origin_stock_no,
                "memo" => $memos[$v->related_id],
                "number" => $v->number,
            ];
            $v1 = $v['origin_stock_no'];
            $arr[$v1][] = $res;
        }
        $data = [];
        foreach ($arr as $k) {
            $sum = 0;
            foreach ($k as $value) {
                $sum += $value['number'];
                $data[] = $value;
            }
            $res = [
                "productId" => '',
                "productName" => '',
                "type" => "",
                "box" => '',
                "rule" => '合计：',
                "surplus" => '',
                "orderNo" => '',
                "Valid" => '',
                "stock" => $k[0]['stock'],
                "memo" => '',
                "number" => $sum,
            ];
            $data[] = $res;
        }
        $template->cloneRow('productName', count($data));
        foreach ($data as $key => $value) {
            $i = $key + 1;
            $template->setValue('productName#' . $i, $value['productName']);
            $template->setValue('type#' . $i, $value['type']);
            $template->setValue('box#' . $i, $value['box']);
            $template->setValue('rule#' . $i, $value['rule']);
            $template->setValue('surplus#' . $i, $value['surplus']);
            $template->setValue('Valid#' . $i, $value['Valid']);
            $template->setValue('orderNo#' . $i, $value['orderNo']);
            $template->setValue('number#' . $i, $value['number']);
            $template->setValue('stock#' . $i, $value['stock']);
            $template->setValue('memo#' . $i, $value['memo']);
        }
        $name = date('YmdHis') . rand('1', '999');
        $filename = storage_path() . '/' . $name . '.docx';
        $template->saveAs($filename);
        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function export(Request $request)
    {
        $parm = $request->all();
        if ($parm['id']) {
            $pieces = explode(",", $parm['id']);
            $salesOuts = Adjust::whereIn('AdjustNo', $pieces)->with(['customer', 'tag'])->get();
        } else {
            $querys = json_decode($parm['query']);
            $params = $cparams = $tparams = [];
            $params = [['InOutFlg', '=', '1']];
            if (isset($querys->AdjustNo)) $params[] = ['AdjustNo', 'like', '%' . $querys->AdjustNo];
            if (isset($querys->OutStcNo)) $params[] = ['OutStcNo', 'like', '%' . $querys->OutStcNo];
            if (isset($querys->FineFlg)) $params[] = ['FineFlg', 'like', '%' . $querys->FineFlg];
            if (isset($querys->created_at) && $querys->created_at[0] && $querys->created_at[0] != 'null') {
                $starttime = date('Y-m-d H:i:s', strtotime($querys->created_at[0]));
                $endtime = date('Y-m-d H:i:s', strtotime($querys->created_at[1]));
                $params[] = ['created_at', '>=', $starttime];
                $params[] = ['created_at', '<=', $endtime];
            }
            if (isset($querys->status) && in_array($querys->status, ['复核中', '待发运', '发货完成', '已回传'])) {
                $tparams[] = ['status', '=', $querys->status];
            }
            if (isset($querys->ShopSignNM)) $cparams[] = ['ShopSignNM', 'like', '%' . $querys->ShopSignNM . '%'];
            $salesOuts = Adjust::where($params)
                ->whereHas('customer', function ($q) use ($cparams) {
                    $q->where($cparams);
                })
                ->whereHas('tag', function ($q) use ($tparams, $querys) {
                    $q->where($tparams);
                    if (isset($querys->status) && $querys->status == '未处理') {
                        $q->whereNull('status');
                    }
                })
                ->with(['customer', 'tag'])->get();
        }

        foreach ($salesOuts as $salesOut) {
            $relatedIds[] = $salesOut->id;
            $tmp = str_replace('<', '', $salesOut->Memo);
            $tmp = str_replace('(', '（', $tmp);
            $tmp = str_replace(')', '）', $tmp);
            $tmp = str_replace('>', '', $tmp);
            $memos[$salesOut->id] = $tmp;
        }
        if (empty($relatedIds)) return '未拣货无法导出';
        $data = GoodsRecord::whereIn('related_id', $relatedIds)->where('type', 'adj_out_dirt')->with(['product'])->get();
        $res = [['品名', '新产品代码', '产品代码', '客户代码', '客户名', '有效期', '出入库类型', '数量', '状态', '库位号', 'SAP单号', '出库单号', '备注', '创建时间']];
        foreach ($data as $key => $value) {
            $odd = Adjust::where('id', $value['related_id'])->with(['customer', 'tag'])->first();
            $res[] = [
                $value['product']['PRODCHINM'],
                $value['product']['NewPRODUCTCD'],
                $value['product']['PRODUCTCD'],
                $odd->CustomerCD,
                $odd['customer']['ShopSignNM'],
                $value['available_time'],
                $value['type'],
                $value['number'],
                $odd['tag']->status,
                $value['origin_stock_no'],
                $odd->AdjustNo,
                $odd->OutStcNo,
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

    public function deep_in_array($value, $array)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item->unit_name == $value) {
                    return true;
                } else {
                    continue;
                }
            }
            if (in_array($value, $item)) {
                return true;
            } else if (deep_in_array($value, $item)) {
                return true;
            }
        }
        return false;
    }

    public function binning(Request $request)
    {
        $all = $request->all();
        $row = DB::table('binning as b')
            ->leftJoin('product as p', 'b.product_id', '=', 'p.id')
            ->where('vbeln', $all['id'])
            ->pluck('p.id')
            ->toArray();
        if (count($row) == 0) {
            return  '未扫入装箱数据';
        }
        $result_01 = array_flip($row);
        $result_02 = array_flip($result_01);
        $result    = array_merge($result_02);

        $unit = [];
        foreach ($result as $ro => $r) {
            $array = DB::table('unit')->where('product_id', $r)->get()->toArray();
            $res = $this->deep_in_array('箱', $array);
            if ($res != 'ture') {
                if (empty($array)) {
                    $info = DB::table('product')->where('id', $r)->get();
                    $unit[] = $info[0]->PRODUCTCD;
                }
                //                $list = DB::table('product')->where('id',$array[0]->product_id)->get();
                //                $unit[] = $list[0]->NewPRODUCTCD;
            }
            continue;
        }
        if (!empty($unit)) {
            $i = implode(",", $unit);
            return $i . '没有填写箱规';
        }
        $arr = [];
        foreach ($result as $ro => $r) {
            $array = DB::table('unit')->where('product_id', $r)->get()->toArray();
            $res = $this->deep_in_array('箱', $array);
            if ($res != 'ture') {
                $list = DB::table('product')->where('id', $array[0]->product_id)->get();
                $arr[] = $list[0]->PRODUCTCD;
            }
            continue;
        }
        if (!empty($arr)) {
            $i = implode(",", $arr);
            return $i . '没有写入箱规';
        }
        foreach ($result as $ro => $r) {
            $array = DB::table('unit')->where('product_id', $r)->get()->toArray();
            $res = $this->deep_in_array('箱', $array);
            if ($res != 'ture') {
                if (empty($array)) {
                    $info = DB::table('product')->where('id', $r)->get();
                    return $info[0]->PRODUCTCD . '没有填写箱规';
                }
                $list = DB::table('product')->where('id', $array[0]->product_id)->get();
                return $list[0]->PRODUCTCD . '没有写入箱规';
            }
            continue;
        }

        $data = DB::table('binning as b')
            ->leftJoin('product as p', 'b.product_id', '=', 'p.id')
            ->leftJoin('unit as u', 'u.product_id', '=', 'p.id')
            ->select('b.vbeln', 'b.case', 'b.available_time', 'b.number', 'p.PRODUCTCD', 'p.PRODCHINM', 'u.number as sum')
            ->where('vbeln', $all['id'])
            ->where('u.unit_name', '箱')
            ->orderBy('b.case', 'asc')
            ->orderBy('p.PRODUCTCD', 'asc')
            ->get();
        $ord = DB::table('adj_out_dirt as o')
            ->leftJoin('customer as c', 'o.CustomerCd', '=', 'c.CUSTOMERCD')
            ->select('c.ShopSignNM')
            ->where('o.AdjustNo', $data['0']->vbeln)
            ->get();
        $res = [['资生堂(中国)投资有限公司'], [], ['客户名称:', $ord[0]->ShopSignNM], ['客户单号:', $data['0']->vbeln], ['箱号', '', '商品代码', '效期', '产品名称', '箱数', '数量']];
        $result = [];

        foreach ($data as $key => $value) {
            if ($value->sum == $value->number) {
                $result[] = [
                    'vbeln' => $value->vbeln,
                    'case' => $value->case,
                    'available_time' => $value->available_time,
                    'number' => $value->number,
                    'PRODUCTCD' => $value->PRODUCTCD,
                    'PRODCHINM' => $value->PRODCHINM,
                    'type' => "box",
                    'sum' => $value->sum,
                ];
            } else {
                $result[] = [
                    'vbeln' => $value->vbeln,
                    'case' => $value->case,
                    'available_time' => $value->available_time,
                    'number' => $value->number,
                    'PRODUCTCD' => $value->PRODUCTCD,
                    'PRODCHINM' => $value->PRODCHINM,
                    'type' => "branch",
                    'sum' => $value->sum,
                ];
            }
        }
        $pre = $prf = [];
        $pre_key = '';
        foreach ($result as $k => $info) {
            $key = $info['PRODUCTCD'] . '-' . $info['available_time'] . '-' . $info['type']. '-'.$info['case'];
            if ($k === 0) {
                $pre[$key] = $info;
                $pre_key = $key;
                continue;
            }
            if (array_key_exists($key, $pre)) {
                $pre[$key]['number'] += $info['number'];
            } else {
                $prf[] = $pre[$pre_key];
                $pre = [];
                $pre_key = $key;
                $pre[$key] = $info;
            }
        }
        // 最后一条特殊处理
        $prf[] = $pre[$pre_key];
        foreach ($prf as $key => $val) {
            $res[] = [
                $val['case'],
                $val['type'] == 'box' ? $val['number'] <= $val['sum'] ? '' : $val['number'] / $val['sum'] + $val['case'] - 1 : '',
                $val['PRODUCTCD'],
                $val['available_time'],
                $val['PRODCHINM'],
                $val['type'] == 'box' ? $val['number'] <= $val['sum'] ? 1 : $val['number'] / $val['sum'] : 1,
                $val['number'],
            ];
        }
        $sum = [];
        $case = [];
        foreach ($result as $re => $i) {
            $sum[] = $i['number'];
            $case[] = $i['case'];
        }
        $res[] = [
            '总计',
            '',
            '',
            '',
            '',
            count(array_unique($case)),
            array_sum($sum),
        ];
        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->cell('E3', function ($cell) use ($res) {
                    $cell->setValue('共' . end($res)[5] . '箱')->setFontSize(30);
                });
                $sheet->cell('B2', function ($cell) use ($res) {
                    $cell->setValue('客户名称:')->setFontSize(10);
                });
                $sheet->cell('A4', function ($cell) use ($res) {
                    $cell->setValue('装箱汇总单')->setFontSize(15);
                });

                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),
                    ),
                );
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setWidth(100);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setWidth(100);
                $sheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E')->getAlignment()->setShrinkToFit(true);
                $sheet->getStyle('E')->getAlignment()->setWrapText(true);
                $sheet->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                $sheet->rows($res);
                $sheet->mergeCells('A9:B9');
                $sheet->mergeCells('A4:G4');
                $sheet->mergeCells('B7:G7');
                $sheet->mergeCells('B8:G8');
                $sheet->mergeCells('A5:G5');
                $sheet->mergeCells('E3:G3');

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path() . '/image/excel_log.png');
                $objDrawing->setHeight(350);
                $objDrawing->setWidth(300);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setOffsetX(100); //写入图片在指定格中的X坐标值
                $objDrawing->setOffsetY(-25); //写入图片在指定格中的Y坐标值
                $objDrawing->setRotation(1); //设置旋转角度
                $objDrawing->getShadow()->setVisible(true); //
                $objDrawing->getShadow()->setDirection(50); //
                $objDrawing->setWorksheet($sheet);

                $data = [];
                $tmp = '';
                $flag = '';
                for ($i = 5; $i < count($res); $i++) {
                    if ($tmp == $res[$i][0]) {
                        $data[$res[$i][0]]['sta'] = $flag;
                        $data[$res[$i][0]]['end'] = $i;
                    } else {
                        $flag = $i;
                        $tmp = $res[$i][0];
                    }
                }
                foreach ($data as $i => $item) {
                    $A = $item['sta'] + 5;
                    $B = $item['end'] + 5;
                    $sheet->mergeCells('A' . $A . ':' . 'A' . $B);
                    $sheet->mergeCells('F' . $A . ':' . 'F' . $B);
                    $sheet->getStyle('A' . $A)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $A)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                $sheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $G = 9 + count($res) - 6;
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A9:' . 'G' . $G)->applyFromArray($styleThinBlackBorderOutline);
            });
        })->export('xls');
    }

    public function pxList(Request $request)
    {
        $res = DB::table('binning')->where(['vbeln' => $request->vbeln])->get();
        if (empty($res)) return sendData(402, '未找到拼箱数据');
        $exist = [];
        $list = [];
        foreach ($res as $key => $value) {
            $unit = DB::table('product as p')
                ->leftJoin('unit as u', 'p.id', '=', 'u.product_id')
                ->select('u.number')
                ->where('p.id', $value->product_id)
                ->where('u.unit_name', '箱')
                ->get();
            if (count($unit) == 0) {
                $list[] = $value->PRODUCTCD;
            }
        }
        if (!empty($list)) {
            $i = implode(",", array_unique($list));
            return sendData(402, $i . '没有填写箱规');
        }
        foreach ($res as $key => $value) {
            $unit = DB::table('product as p')
                ->leftJoin('unit as u', 'p.id', '=', 'u.product_id')
                ->select('u.number')
                ->where('p.id', $value->product_id)
                ->where('u.unit_name', '箱')
                ->get();
            if ($unit[0]->number - $value->number  == 0) {
                continue;
            } else {
                $exist[] = $value->case;
            }
        }
        $result_01 = array_flip($exist);
        $result_02 = array_flip($result_01);
        $data    = array_merge($result_02);
        sort($data);
        return sendData(200, '', $data);
    }

    public function downloadPX(Request $request)
    {
        if (!$request->vbeln || !$request->case) return '参数错误！';
        $data = DB::table('binning as b')
            ->leftJoin('product as p', 'b.product_id', '=', 'p.id')
            ->select('b.vbeln', 'b.case', 'b.available_time', 'b.number', 'p.PRODUCTCD', 'p.PRODCHINM')
            ->where(['b.vbeln' => $request->vbeln, 'b.case' => $request->case])
            ->orderBy('p.PRODUCTCD', 'asc')
            ->get();
        if (count($data) == 0) {
            return '数据获取失败';
        }
        $pre = $prf = [];
        $pre_key = '';
        foreach ($data as $k => $info) {
            $key = $info->PRODUCTCD . '-' . $info->available_time;
            if ($k === 0) {
                $pre[$key] = $info;
                $pre_key = $key;
                continue;
            }
            if (array_key_exists($key, $pre)) {
                $pre[$key]->number += $info->number;
            } else {
                $prf[] = $pre[$pre_key];
                $pre = [];
                $pre_key = $key;
                $pre[$key] = $info;
            }
        }
        // 最后一条特殊处理
        $prf[] = $pre[$pre_key];
        $ord = DB::table('adj_out_dirt as o')
            ->leftJoin('customer as c', 'o.CustomerCd', '=', 'c.CUSTOMERCD')
            ->select('c.ShopSignNM')
            ->where('o.AdjustNo', $data['0']->vbeln)
            ->get();
        $res = [['客户名', $ord[0]->ShopSignNM, '', '订单号', $data['0']->vbeln], ['箱号', '', $request->case], [], ['商品代码', '有效期', '品名', '数量', '备注']];
        foreach ($prf as $key => $value) {
            $res[] = [
                $value->PRODUCTCD,
                $value->available_time,
                $value->PRODCHINM,
                'number' => $value->number,
                ''
            ];
        }
        $sum = [];
        $res[] = [];
        foreach ($res as $re => $i) {
            if (empty($i['number'])) {
                continue;
            }
            $sum[] = $i['number'];
        }
        $res[] = [
            '总数',
            '',
            '',
            array_sum($sum),
        ];
        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->cell('A5', function ($cell) use ($res) {
                    $cell->setValue('拼箱清单')->setFontSize(20);
                });
                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),
                    ),
                );
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path() . '/image/excel_log.png');
                $objDrawing->setHeight(350);
                $objDrawing->setWidth(300);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setOffsetX(100); //写入图片在指定格中的X坐标值
                $objDrawing->setOffsetY(-25); //写入图片在指定格中的Y坐标值
                $objDrawing->setRotation(1); //设置旋转角度
                $objDrawing->getShadow()->setVisible(true); //
                $objDrawing->getShadow()->setDirection(50); //
                $objDrawing->setWorksheet($sheet);
                $sheet->rows($res);
                $sheet->mergeCells('A5:E5');
                $sheet->mergeCells('B6:C6');
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $G =  9 + count($res) - 6;
                $sheet->getStyle('A9:' . 'E' . $G)->applyFromArray($styleThinBlackBorderOutline);
            });
        })->export('xls');
    }
    public function downloadPXHZ(Request $request)
    {
        $all = $request->all();
        $row = DB::table('binning as b')
            ->leftJoin('product as p', 'b.product_id', '=', 'p.id')
            ->where('vbeln', $all['id'])
            ->pluck('p.id')
            ->toArray();
        if (count($row) == 0) {
            return  '未扫入装箱数据';
        }
        $result_01 = array_flip($row);
        $result_02 = array_flip($result_01);
        $result    = array_merge($result_02);
        $unit = [];
        foreach ($result as $ro => $r) {
            $array = DB::table('unit')->where('product_id', $r)->get()->toArray();
            $res = $this->deep_in_array('箱', $array);
            if ($res != 'ture') {
                if (empty($array)) {
                    $info = DB::table('product')->where('id', $r)->get();
                    $unit[] = $info[0]->PRODUCTCD;
                }
                //                $list = DB::table('product')->where('id',$array[0]->product_id)->get();
                //                $unit[] = $list[0]->NewPRODUCTCD;
            }
            continue;
        }
        if (!empty($unit)) {
            $i = implode(",", $unit);
            return $i . '没有填写箱规';
        }
        $arr = [];
        foreach ($result as $ro => $r) {
            $array = DB::table('unit')->where('product_id', $r)->get()->toArray();
            $res = $this->deep_in_array('箱', $array);
            if ($res != 'ture') {
                $list = DB::table('product')->where('id', $array[0]->product_id)->get();
                $arr[] = $list[0]->PRODUCTCD;
            }
            continue;
        }
        if (!empty($arr)) {
            $i = implode(",", $arr);
            return $i . '没有写入箱规';
        }
        foreach ($result as $ro => $r) {
            $array = DB::table('unit')->where('product_id', $r)->get()->toArray();
            $res = $this->deep_in_array('箱', $array);
            if ($res != 'ture') {
                if (empty($array)) {
                    $info = DB::table('product')->where('id', $r)->get();
                    return $info[0]->PRODUCTCD . '没有填写箱规';
                }
                $list = DB::table('product')->where('id', $array[0]->product_id)->get();
                return $list[0]->PRODUCTCD . '没有写入箱规';
            }
            continue;
        }

        $data = DB::table('binning as b')
            ->leftJoin('product as p', 'b.product_id', '=', 'p.id')
            ->leftJoin('unit as u', 'u.product_id', '=', 'p.id')
            ->select('b.vbeln', 'b.case', 'b.available_time', 'b.number', 'p.PRODUCTCD', 'p.PRODCHINM', 'u.number as sum')
            ->where('vbeln', $all['id'])
            ->where('u.unit_name', '箱')
            ->orderBy('b.case', 'asc')
            ->orderBy('p.PRODUCTCD', 'asc')
            ->get();
        $ord = DB::table('adj_out_dirt as o')
            ->leftJoin('customer as c', 'o.CustomerCd', '=', 'c.CUSTOMERCD')
            ->select('c.ShopSignNM')
            ->where('o.AdjustNo', $data['0']->vbeln)
            ->get();
        $res = [['资生堂(中国)投资有限公司'], [''], ['客户名称:', $ord[0]->ShopSignNM], ['客户单号:', $data['0']->vbeln], ['箱号', '', '商品代码', '效期', '产品名称', '箱数', '数量']];
        $result = [];
        foreach ($data as $key => $value) {
            if ($value->sum == $value->number) {
                $result[] = [
                    'vbeln' => $value->vbeln,
                    'case' => $value->case,
                    'available_time' => $value->available_time,
                    'number' => $value->number,
                    'PRODUCTCD' => $value->PRODUCTCD,
                    'PRODCHINM' => $value->PRODCHINM,
                    'type' => "box",
                    'sum' => $value->sum,
                ];
            } else {
                $result[] = [
                    'vbeln' => $value->vbeln,
                    'case' => $value->case,
                    'available_time' => $value->available_time,
                    'number' => $value->number,
                    'PRODUCTCD' => $value->PRODUCTCD,
                    'PRODCHINM' => $value->PRODCHINM,
                    'type' => "branch",
                    'sum' => $value->sum,
                ];
            }
        }
        $pre = $prf = [];
        $pre_key = '';
        foreach ($result as $k => $info) {
            $key = $info['PRODUCTCD'] . '-' . $info['available_time'] . '-' . $info['type']. '-'.$info['case'];
            if ($k === 0) {
                $pre[$key] = $info;
                $pre_key = $key;
                continue;
            }
            if (array_key_exists($key, $pre)) {
                $pre[$key]['number'] += $info['number'];
            } else {
                $prf[] = $pre[$pre_key];
                $pre = [];
                $pre_key = $key;
                $pre[$key] = $info;
            }
        }
        // 最后一条特殊处理
        $prf[] = $pre[$pre_key];
        foreach ($prf as $key => $val) {
            $res[] = [
                $val['case'],
                $val['type'] == 'box' ? $val['number'] <= $val['sum'] ? '' : $val['number'] / $val['sum'] + $val['case'] - 1 : '',
                $val['PRODUCTCD'],
                $val['available_time'],
                $val['PRODCHINM'],
                $val['type'] == 'box' ? $val['number'] <= $val['sum'] ? 1 : $val['number'] / $val['sum'] : 1,
                $val['number'],
            ];
        }
        $sum = [];
        $case = [];
        foreach ($result as $re => $i) {
            $sum[] = $i['number'];
            $case[] = $i['case'];
        }
        $res[] = [
            '总计',
            '',
            '',
            '',
            '',
            count(array_unique($case)),
            array_sum($sum),
        ];

        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($res,$all) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $sheet->cell('E3', function ($cell) use ($res) {
                    $cell->setValue('共' . end($res)[5] . '箱')->setFontSize(30);
                });
                $sheet->cell('B2', function ($cell) use ($res) {
                    $cell->setValue('客户名称:')->setFontSize(10);
                });
                $sheet->cell('A4', function ($cell) use ($res) {
                    $cell->setValue('装箱汇总单')->setFontSize(15);
                });

                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),
                    ),
                );
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setWidth(100);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setWidth(100);
                $sheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E')->getAlignment()->setShrinkToFit(true);
                $sheet->getStyle('E')->getAlignment()->setWrapText(true);
                $sheet->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                $sheet->rows($res);
                $sheet->mergeCells('A9:B9');
                $sheet->mergeCells('A4:G4');
                $sheet->mergeCells('B7:G7');
                $sheet->mergeCells('B8:G8');
                $sheet->mergeCells('A5:G5');
                $sheet->mergeCells('E3:G3');

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path() . '/image/excel_log.png');
                $objDrawing->setHeight(350);
                $objDrawing->setWidth(300);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setOffsetX(100); //写入图片在指定格中的X坐标值
                $objDrawing->setOffsetY(-25); //写入图片在指定格中的Y坐标值
                $objDrawing->setRotation(1); //设置旋转角度
                $objDrawing->getShadow()->setVisible(true); //
                $objDrawing->getShadow()->setDirection(50); //
                $objDrawing->setWorksheet($sheet);

                $data = [];
                $tmp = '';
                $flag = '';
                for ($i = 5; $i < count($res); $i++) {
                    if ($tmp == $res[$i][0]) {
                        $data[$res[$i][0]]['sta'] = $flag;
                        $data[$res[$i][0]]['end'] = $i;
                    } else {
                        $flag = $i;
                        $tmp = $res[$i][0];
                    }
                }
                foreach ($data as $i => $item) {
                    $A = $item['sta'] + 5;
                    $B = $item['end'] + 5;
                    $sheet->mergeCells('A' . $A . ':' . 'A' . $B);
                    $sheet->mergeCells('F' . $A . ':' . 'F' . $B);
                    $sheet->getStyle('A' . $A)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $A)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                $sheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $G = 9 + count($res) - 6;
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A9:' . 'G' . $G)->applyFromArray($styleThinBlackBorderOutline);
            });
            $show = DB::table('binning')->where(['vbeln' => $res[3][1]])->get();
            if (empty($show)) return sendData(402, '未找到拼箱数据');
            $exist = [];
            $list = [];
            foreach ($show as $key => $value) {
                $unit = DB::table('product as p')
                    ->leftJoin('unit as u', 'p.id', '=', 'u.product_id')
                    ->select('u.number')
                    ->where('p.id', $value->product_id)
                    ->where('u.unit_name', '箱')
                    ->get();
                if (count($unit) == 0) {
                    $list[] = $value->PRODUCTCD;
                }
            }
            if (!empty($list)) {
                $i = implode(",", array_unique($list));
                return sendData(402, $i . '没有填写箱规');
            }
            foreach ($show as $key => $value) {
                $unit = DB::table('product as p')
                    ->leftJoin('unit as u', 'p.id', '=', 'u.product_id')
                    ->select('u.number')
                    ->where('p.id', $value->product_id)
                    ->where('u.unit_name', '箱')
                    ->get();

                if ($unit[0]->number - $value->number  == 0) {
                    continue;
                } else {
                    $exist[] = $value->case;
                }
            }
            $result_01 = array_flip($exist);
            $result_02 = array_flip($result_01);
            $data    = array_merge($result_02);
            sort($data);
            foreach ($data as $re => $val) {
                $row = DB::table('binning as b')
                    ->leftJoin('product as p', 'b.product_id', '=', 'p.id')
                    ->select('b.vbeln', 'b.case', 'b.available_time', 'b.number', 'p.PRODUCTCD', 'p.PRODCHINM')
                    ->where(['b.vbeln' => $res[3][1], 'b.case' => $val])
                    ->orderBy('p.PRODUCTCD', 'asc')
                    ->get();
                if (count($row) == 0) {
                    return '数据获取失败';
                }
                $pre = $prf = [];
                $pre_key = '';
                foreach ($row as $k => $info) {
                    $key = $info->PRODUCTCD . '-' . $info->available_time;
                    if ($k === 0) {
                        $pre[$key] = $info;
                        $pre_key = $key;
                        continue;
                    }
                    if (array_key_exists($key, $pre)) {
                        $pre[$key]->number += $info->number;
                    } else {
                        $prf[] = $pre[$pre_key];
                        $pre = [];
                        $pre_key = $key;
                        $pre[$key] = $info;
                    }
                }
                // 最后一条特殊处理
                $prf[] = $pre[$pre_key];
                $ord = DB::table('adj_out_dirt as o')
                    ->leftJoin('customer as c', 'o.CustomerCd', '=', 'c.CUSTOMERCD')
                    ->select('c.ShopSignNM')
                    ->where('o.AdjustNo', $row['0']->vbeln)
                    ->get();
                $k = [['客户名', $ord[0]->ShopSignNM, '', '订单号', $res[3][1]], ['箱号', '', $val], [], ['商品代码', '有效期', '品名', '数量', '备注']];
                foreach ($prf as $key => $value) {
                    $k[] = [
                        $value->PRODUCTCD,
                        $value->available_time,
                        $value->PRODCHINM,
                        'number' => $value->number,
                        ''
                    ];
                }
                $sum = [];
                $k[] = [];
                foreach ($k as $re => $i) {
                    if (empty($i['number'])) {
                        continue;
                    }
                    $sum[] = $i['number'];
                }
                $k[] = [
                    '总数',
                    '',
                    '',
                    array_sum($sum),
                ];
                $excel->sheet("$val", function ($sheet) use ($k) {
                    $sheet->cell('A5', function ($cell) use ($k) {
                        $cell->setValue('拼箱清单')->setFontSize(20);
                    });
                    $styleThinBlackBorderOutline = array(
                        'borders' => array(
                            'allborders' => array( //设置全部边框
                                'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                            ),
                        ),
                    );
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path() . '/image/excel_log.png');
                    $objDrawing->setHeight(350);
                    $objDrawing->setWidth(300);
                    $objDrawing->setCoordinates('A2');
                    $objDrawing->setOffsetX(100); //写入图片在指定格中的X坐标值
                    $objDrawing->setOffsetY(-25); //写入图片在指定格中的Y坐标值
                    $objDrawing->setRotation(1); //设置旋转角度
                    $objDrawing->getShadow()->setVisible(true); //
                    $objDrawing->getShadow()->setDirection(50); //
                    $objDrawing->setWorksheet($sheet);
                    $sheet->rows($k);
                    $sheet->mergeCells('A5:E5');
                    $sheet->mergeCells('B6:C6');
                    $sheet->getStyle('A5')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $G =  9 + count($k) - 6;
                    $sheet->getStyle('A9:' . 'E' . $G)->applyFromArray($styleThinBlackBorderOutline);
                });
            }
            DB::table('adj_out_dirt')->where('AdjustNo', $all['id'])->update(['print_status'=>'已打印']);
        })->export('xls');
    }
}
