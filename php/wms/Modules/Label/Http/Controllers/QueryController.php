<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Label\Models\ItemStock;
use Modules\Label\Models\QueryResult;
use Illuminate\Support\Facades\DB;
use Excel;
use PHPExcel_IOFactory;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\Invoice;
use Modules\Label\Models\Item;

class QueryController extends BaseController
{
    // 分页获取查询记录列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $where = [];
        if ($request->get('invoice_no')) $where[] =['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('location_no')) $where[] = ['location_no', '=', $request->get('location_no')];
        if ($request->get('task_no')) $where[] = ['task_no', '=', $request->get('task_no')];
        if ($request->get('support_no')) $where[] = ['support_no', '=', $request->get('support_no')];
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];
        if ($request->get('box_mark')) $where[] = ['box_mark', '=', $request->get('box_mark')];
        if ($request->get('case_mark')) $where[] = ['case_mark', '=', $request->get('case_mark')];
        if ($request->get('branch_mark')) $where[] = ['branch_mark', '=', $request->get('branch_mark')];
        if($request->get('status') != '全部'){
            if ($request->get('status')) $where[] = ['status', '=', $request->get('status')];
        }
        $querys = QueryResult::where($where)->orderBy($sort, 'desc')->paginate($limit);
        return $this->sendData(200, '', $querys);
    }

    public function checkInvoice(Request $request)
    {
        $invoice_no = $request->get('invoice_no');

        $goodsIdsList = $request->get('goodsIdsList');
        if (!$invoice_no) return $this->sendData(402, '发票号不能为空');
        if (!$goodsIdsList) return $this->sendData(402, '托号不能为空');
        $search = [
            ['invoice_no', '=', $invoice_no],
            ['status', '=', 2],
            ['expired_at', '=', ''],
        ];
        $stocks = ItemStock::where($search)->whereIn('support_no',$goodsIdsList)->with('item')->get()->toArray();
        if (empty($stocks)) return $this->sendData(402, '该发票号暂无可导出数据，或已导入有效期');
        $data = [];
        foreach ($stocks as $stock) {
            if (!$stock['item']) return $this->sendData(402, $stock['material_code'] . '未找到基础信息');
            if (!$stock['item']['box_num']) {
                $data[$stock['material_code']] = '无箱规;';
            }
            if (!$stock['item']['case_num']) {
                if (!isset($data[$stock['material_code']])) $data[$stock['material_code']] = '';
                $data[$stock['material_code']] .= '无盒规;';
            }
        }

        return $this->sendData(200, '', $data);
    }
    public function checkInvoices(Request $request)
    {
        $invoice_no = $request->get('invoice_no');

        if (!$invoice_no) return $this->sendData(402, '发票号不能为空');
        $search = [
            ['invoice_no', '=', $invoice_no],
            ['status', '=', 2],
            ['expired_at', '=', ''],
        ];
        $stocks = ItemStock::where($search)->with('item')->get()->toArray();
        if (empty($stocks)) return $this->sendData(402, '该发票号暂无可导出数据，或已导入有效期');
        $data = [];
        foreach ($stocks as $stock) {
            if (!$stock['item']) return $this->sendData(402, $stock['material_code'] . '未找到基础信息');
            if (!$stock['item']['box_num']) {
                $data[$stock['material_code']] = '无箱规;';
            }
            if (!$stock['item']['case_num']) {
                if (!isset($data[$stock['material_code']])) $data[$stock['material_code']] = '';
                $data[$stock['material_code']] .= '无盒规;';
            }
        }

        return $this->sendData(200, '', $data);
    }
    public function exports(Request $request)
    {
        $invoice_no = $request->get('invoice_no');
        $goodsIdsList = explode(",", $request->get('goodsIdsList'));
        if (!$invoice_no) return $this->sendData(402, '发票号不能为空');
        if (!$goodsIdsList) return $this->sendData(402, '托号不能为空');
        QueryResult::where('invoice_no',$invoice_no)->whereIn('support_no',$goodsIdsList)->update(['status'=>'已打印']);
        $search = [
            ['invoice_no', '=', $invoice_no],
            ['status', '=', 2],
            ['expired_at', '=', ''],
        ];
        $stocks = ItemStock::where($search)
            ->select(['invoice_no', 'item_no', 'material_code', 'item_name', 'support_no', 'box_mark', 'case_mark', 'branch_mark', DB::raw('sum(num) as total')])
            ->groupBy(['invoice_no', 'item_no', 'material_code', 'item_name', 'support_no', 'box_mark', 'case_mark', 'branch_mark'])
            ->whereIn('support_no',$goodsIdsList)
            ->with('item')
            ->get()
            ->toArray();
        
        $fileName = str_replace('/', '-', $invoice_no) . '托单';
        return Excel::create($fileName, function ($excel) use ($invoice_no, $stocks) {
            $excel->setTitle('查询导出');
            // 第二个sheet
            foreach ($stocks as $value) {
                $exist[] = $value['support_no'];
            }
            $result_01 = array_flip($exist);
            $result_02 = array_flip($result_01);
            $data    = array_merge($result_02);
            sort($data);
            foreach ($data as $key){
                $search = [
                    ['invoice_no', '=', $invoice_no],
                    ['status', '=', 2],
                    ['expired_at', '=', ''],
                    ['support_no', '=', $key],
                ];
                $stocks = ItemStock::where($search)
                    ->select(['invoice_no', 'item_no', 'material_code', 'item_name', 'support_no', 'box_mark', 'case_mark', 'branch_mark', DB::raw('sum(num) as total')])
                    ->groupBy(['invoice_no', 'item_no', 'material_code', 'item_name', 'support_no', 'box_mark', 'case_mark', 'branch_mark'])
                    ->with('item')
                    ->get()
                    ->toArray();
            $keyName = str_replace('/', '-', $key);
            $excel->sheet("$keyName", function ($sheet) use ($stocks) {
                $data = [
                    [$stocks[0]['support_no']],
                    ['发票号', '托号', '料号', '箱', '中盒', '最小', '箱数', '中盒', '最小单位', '异常描述', '', '', '', '', '', '', '备注'],
                    ['', '', '', '制造记号1', '制造记号2', '制造记号3', '', '', '', '外箱', '', '中盒', '', '产品', '', '其他', ''],
                    ['', '', '', '', '', '', '', '', '', '破损', '变形', '破损', '变形', '破损', '变形', '', ''],
                ];
                $total = 0;
                foreach ($stocks as $stock) {
                    $stockRow = [
                        $stock['invoice_no'],
                        $stock['support_no'],
                        $stock['item_no'],
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['box_mark'] ? $stock['box_mark'] : '/'),
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['case_mark'] ? $stock['case_mark'] : '/'),
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['branch_mark'] ? $stock['branch_mark'] : '/'),
                        $stock['item']['box_num'] ? ceil($stock['total'] / $stock['item']['box_num']) : $stock['total'],
                        $stock['item']['case_num'] ? ceil($stock['total'] / $stock['item']['case_num']) : $stock['total'],
                        $stock['total'],
                    ];
                    $data[] = $stockRow;
                    $total += $stock['total'];
                }
                $data[] = ['', '', '', '', '', '', '', '', $total];
                $sheet->fromArray($data, null, 'A1', false, false);
                // 样式设置
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  14,
                        'bold'      =>  false
                    ),
                ));
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontFamily('宋体');
                });
                $sheet->row(2, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontFamily('宋体');
                });
                $sheet->row(3, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontFamily('宋体');
                });
                $style_array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle('A2:Q' . (count($stocks) + 4))->applyFromArray($style_array);
                $sheet->setWidth('A', 12);
                $sheet->setWidth('B', 6);
                $sheet->setWidth('C', 10);
                $sheet->setWidth('D', 11);
                $sheet->setWidth('E', 11);
                $sheet->setWidth('F', 11);
                $sheet->setWidth('G', 8);
                $sheet->setWidth('H', 8);
                $sheet->setWidth('I', 8);
                $sheet->setWidth('J', 5);
                $sheet->setWidth('K', 5);
                $sheet->setWidth('L', 5);
                $sheet->setWidth('M', 5);
                $sheet->setWidth('N', 5);
                $sheet->setWidth('O', 5);
                $sheet->setWidth('P', 5);
                $sheet->setWidth('Q', 10);
                // 合并单元格
                $sheet->mergeCells('A1:Q1');
                $sheet->mergeCells('A2:A4');
                $sheet->mergeCells('B2:B4');
                $sheet->mergeCells('C2:C4');
                $sheet->mergeCells('D2:D3');
                $sheet->mergeCells('E2:E3');
                $sheet->mergeCells('F2:F3');
                $sheet->mergeCells('G2:G4');
                $sheet->mergeCells('H2:H4');
                $sheet->mergeCells('I2:I4');
                $sheet->mergeCells('J2:P2');
                $sheet->mergeCells('J2:K2');
                $sheet->mergeCells('L2:M2');
                $sheet->mergeCells('N2:O2');
                $sheet->mergeCells('P2:P3');
                $sheet->mergeCells('Q2:Q4');
                // 样式设置
                $sheet->cells('A1:Q' . (count($stocks) + 4), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
            }
        })->export('xls');
    }

    public function export(Request $request)
    {
        $invoice_no = $request->get('invoice_no');
        if (!$invoice_no) return $this->sendData(402, '发票号不能为空');

        $search = [
            ['invoice_no', '=', $invoice_no],
            ['status', '=', 2],
            ['expired_at', '=', ''],
        ];
        $stocks = ItemStock::where($search)
            ->select(['invoice_no', 'item_no', 'material_code', 'item_name', 'support_no', 'box_mark', 'case_mark', 'branch_mark', DB::raw('sum(num) as total')])
            ->groupBy(['invoice_no', 'item_no', 'material_code', 'item_name', 'support_no', 'box_mark', 'case_mark', 'branch_mark'])
            ->with('item')
            ->get()
            ->toArray();
        $fileName = str_replace('/', '-', $invoice_no) . '总查询制造记号';
        return Excel::create($fileName, function ($excel) use ($invoice_no, $stocks) {
            $excel->setTitle('查询导出');
            //第一个sheet
            $excel->sheet('对应发票号', function ($sheet) use ($stocks) {
                $data = [
                    [],
                    ['', '贴标日期：', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '发票号：'],
                    ['序号', '发票号', '产品编号', '系统代码', 'SAP代码', '产品名称', '制造记号', "最小单位\r\n制造记号", '赏味日期', '箱型', '产品', '', '', '', '', '', '', '', '差异说明', '', '', '', "登记\r\n入库时间"],
                    ['', '', '', '', '', '', '', '', '', '', '数量', '', '', '', '', '全量', '良品在库', '抽样', '取样', '进口原因'],
                    ['', '', '', '', '', '', '', '', '', '', "总\r\n箱数", '栈板数', '箱入数', '盒入数', '支数', '', '', '', '', "进口\r\n不良", "进口\r\n差异", '备注', '', '生产年月', '保质期', '图片编号'],
                ];
                $total = $index = 0;
                // 重新组织一下stocks
                $tmp = [];
                foreach ($stocks as $stock) {
                    $key = $stock['material_code'] . '_' . $stock['branch_mark'];
                    if (!isset($tmp[$key])) {
                        $tmp[$key] = $stock;
                    } else {
                        $tmp[$key]['total'] += $stock['total'];
                    }
                }
                foreach ($tmp as $stock) {
                    $index++;
                    $stockRow = [
                        $index,
                        $stock['invoice_no'],
                        $stock['item_no'],
                        $stock['item_no'],
                        $stock['material_code'],
                        $stock['item_name'],
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['case_mark'] ? $stock['case_mark'] : '/'),
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['branch_mark'] ? $stock['branch_mark'] : '/'),
                        '',
                        '',
                        $stock['item']['box_num'] ? ceil($stock['total'] / $stock['item']['box_num']) : $stock['total'],
                        '',
                        $stock['item']['box_num'] ? $stock['item']['box_num'] : '/',
                        $stock['item']['case_num'] ? $stock['item']['case_num'] : '/',
                        $stock['total'],
                        $stock['total'],
                    ];
                    $data[] = $stockRow;
                    $total += $stock['total'];
                }
                $data[] = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $total];
                $sheet->fromArray($data, null, 'A1', false, false);
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  11,
                        'bold'      =>  false
                    ),
                ));
                $sheet->row(2, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontSize('12');
                    $row->setFontFamily('微软雅黑');
                });
                $sheet->row(3, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontSize('12');
                    $row->setFontFamily('微软雅黑');
                });
                $sheet->row(4, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontSize('12');
                    $row->setFontFamily('微软雅黑');
                });
                $sheet->row(5, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontSize('12');
                    $row->setFontFamily('微软雅黑');
                });
                // 设置样式
                $style_array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle('A1:Z' . (count($tmp) + 6))->applyFromArray($style_array);
                $sheet->setWidth('A', 8);
                $sheet->setWidth('B', 11);
                $sheet->setWidth('C', 10);
                $sheet->setWidth('D', 15);
                $sheet->setWidth('E', 11);
                $sheet->setWidth('F', 30);
                $sheet->setWidth('G', 11);
                $sheet->setWidth('H', 10);
                $sheet->setWidth('I', 10);
                $sheet->setWidth('J', 8);
                $sheet->setWidth('K', 8);
                $sheet->setWidth('L', 8);
                $sheet->setWidth('M', 8);
                $sheet->setWidth('N', 8);
                $sheet->setWidth('O', 8);
                $sheet->setWidth('P', 8);
                $sheet->setWidth('Q', 8);
                $sheet->setWidth('R', 8);
                $sheet->setWidth('S', 8);
                $sheet->setWidth('T', 8);
                $sheet->setWidth('U', 8);
                $sheet->setWidth('V', 8);
                $sheet->setWidth('W', 8);
                $sheet->setWidth('X', 13);
                $sheet->setWidth('Y', 8);
                $sheet->setWidth('Z', 8);
                $sheet->getStyle('H3')->getAlignment()->setWrapText(true);
                $sheet->getStyle('K5')->getAlignment()->setWrapText(true);
                $sheet->getStyle('T5')->getAlignment()->setWrapText(true);
                $sheet->getStyle('U5')->getAlignment()->setWrapText(true);
                $sheet->getStyle('W3')->getAlignment()->setWrapText(true);
                // 合并单元格
                $sheet->mergeCells('B2:D2');
                $sheet->mergeCells('S2:T2');
                $sheet->mergeCells('A3:A5');
                $sheet->mergeCells('B3:B5');
                $sheet->mergeCells('C3:C5');
                $sheet->mergeCells('D3:D5');
                $sheet->mergeCells('E3:E5');
                $sheet->mergeCells('F3:F5');
                $sheet->mergeCells('G3:G5');
                $sheet->mergeCells('H3:H5');
                $sheet->mergeCells('I3:I5');
                $sheet->mergeCells('J3:J5');
                $sheet->mergeCells('K3:R3');
                $sheet->mergeCells('S3:U3');
                $sheet->mergeCells('W3:W5');
                $sheet->mergeCells('K4:O4');
                $sheet->mergeCells('P4:P5');
                $sheet->mergeCells('Q4:Q5');
                $sheet->mergeCells('R4:R5');
                $sheet->mergeCells('S4:S5');
                $sheet->mergeCells('T4:U4');
                // 样式设置
                $sheet->cells('A1:Z' . (count($tmp) + 6), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });


            // 第二个sheet
            $excel->sheet(str_replace('/', '-', $invoice_no), function ($sheet) use ($stocks) {
                $data = [
                    ['发票号', '托号', '料号', '箱', '中盒', '最小', '箱数', '中盒', '最小单位', '异常描述', '', '', '', '', '', '', '备注'],
                    ['', '', '', '制造记号1', '制造记号2', '制造记号3', '', '', '', '外箱', '', '中盒', '', '产品', '', '其他', ''],
                    ['', '', '', '', '', '', '', '', '', '破损', '变形', '破损', '变形', '破损', '变形', '', ''],
                ];
                $total = 0;
                foreach ($stocks as $stock) {
                    $stockRow = [
                        $stock['invoice_no'],
                        $stock['support_no'],
                        $stock['item_no'],
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['box_mark'] ? $stock['box_mark'] : '/'),
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['case_mark'] ? $stock['case_mark'] : '/'),
                        $stock['item']['is_mark_valid'] ? '不查' : ($stock['branch_mark'] ? $stock['branch_mark'] : '/'),
                        $stock['item']['box_num'] ? ceil($stock['total'] / $stock['item']['box_num']) : $stock['total'],
                        $stock['item']['case_num'] ? ceil($stock['total'] / $stock['item']['case_num']) : $stock['total'],
                        $stock['total'],
                    ];
                    $data[] = $stockRow;
                    $total += $stock['total'];
                }
                $data[] = ['', '', '', '', '', '', '', '', $total];
                $sheet->fromArray($data, null, 'A1', false, false);
                // 样式设置
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  14,
                        'bold'      =>  false
                    ),
                ));
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontFamily('宋体');
                });
                $sheet->row(2, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontFamily('宋体');
                });
                $sheet->row(3, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setFontFamily('宋体');
                });
                $style_array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle('A1:Q' . (count($stocks) + 4))->applyFromArray($style_array);
                $sheet->setWidth('A', 12);
                $sheet->setWidth('B', 6);
                $sheet->setWidth('C', 10);
                $sheet->setWidth('D', 11);
                $sheet->setWidth('E', 11);
                $sheet->setWidth('F', 11);
                $sheet->setWidth('G', 8);
                $sheet->setWidth('H', 8);
                $sheet->setWidth('I', 8);
                $sheet->setWidth('J', 5);
                $sheet->setWidth('K', 5);
                $sheet->setWidth('L', 5);
                $sheet->setWidth('M', 5);
                $sheet->setWidth('N', 5);
                $sheet->setWidth('O', 5);
                $sheet->setWidth('P', 5);
                $sheet->setWidth('Q', 10);
                // 合并单元格
                $sheet->mergeCells('A1:A3');
                $sheet->mergeCells('B1:B3');
                $sheet->mergeCells('C1:C3');
                $sheet->mergeCells('D2:D3');
                $sheet->mergeCells('E2:E3');
                $sheet->mergeCells('F2:F3');
                $sheet->mergeCells('G1:G3');
                $sheet->mergeCells('H1:H3');
                $sheet->mergeCells('I1:I3');
                $sheet->mergeCells('J1:P1');
                $sheet->mergeCells('J2:K2');
                $sheet->mergeCells('L2:M2');
                $sheet->mergeCells('N2:O2');
                $sheet->mergeCells('P2:P3');
                $sheet->mergeCells('Q1:Q3');
                // 样式设置
                $sheet->cells('A1:Q' . (count($stocks) + 4), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
        })->export('xls');
    }

    public function import(Request $request)
    {
        if (!$request->hasFile('file')) return $this->sendData(402, '上传失败!');
        try {
            $path = $this->saveFile($request->file('file'));
            $inputFileType = PHPExcel_IOFactory::identify($path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($path);
            $sheet = $objPHPExcel->getSheet(0);
            $rows = $sheet->getRowIterator(6);
            $excelData = $errors = [];
            $line = 5;
            foreach ($rows as $row) {
                $cells = $row->getCellIterator();
                $line++;
                $rowData = [];
                $error = [
                    'line' => $line,
                    'message' => '',
                ];
                foreach ($cells as $key => $cell) {
                    $value = (string) $cell->getValue();
                    switch ($key) {
                        case 'B':
                            $rowData['invoice_no'] = $value;
                            break;
                        case 'E':
                            $rowData['material_code'] = $value;
                            break;
                        case 'G':
                            if ($value == '不查') continue 3;
                            if ($value == '/') $value = '';
                            $rowData['case_mark'] = $value;
                            break;
                        case 'H':
                            if ($value == '/') $value = '';
                            $rowData['branch_mark'] = $value;
                            break;
                        case 'I':
                            if (!$value) {
                                $error['message'] .= '赏味日期不能为空;';
                                continue 2;
                            }
                            if (is_numeric($value)) {
                                $rowData['expired_at'] = gmdate('Y-m', \PHPExcel_Shared_Date::ExcelToPHP($value));
                            } else {
                                if (!preg_match('/^\d{4}年\d{1,2}月$/i', $value)) {
                                    $error['message'] .= '赏味日期格式不正确,必须为XXXX年XX月;';
                                    continue 2;
                                }
                                $value = str_replace('月', '', $value);
                                $tmp = explode('年', $value);
                                $rowData['expired_at'] = $tmp[0] . '-' . str_pad($tmp[1], 2, '0', STR_PAD_LEFT);
                            }
                            break;
                        case 'P':
                            $rowData['num'] = $value;
                            break;
                        case 'X':
                            if (!$value) {
                                $error['message'] .= '生产日期不能为空;';
                                continue 2;
                            }
                            if (is_numeric($value)) {
                                $rowData['production_date'] = gmdate('Y-m', \PHPExcel_Shared_Date::ExcelToPHP($value));
                            } else {
                                if (!preg_match('/^\d{4}年\d{1,2}月$/i', $value)) {
                                    $error['message'] .= '生产日期格式不正确,必须为XXXX年XX月;';
                                    continue 2;
                                } else {
                                    $value = str_replace('月', '', $value);
                                    $tmp = explode('年', $value);
                                    $rowData['production_date'] = $tmp[0] . '-' . str_pad($tmp[1], 2, '0', STR_PAD_LEFT);
                                }
                            }
                            break;
                        case 'Y':
                            if ($value <= 0)  $error['message'] .= '保质期不能小于0;';
                            $rowData['valid_month'] = $value;
                            break;
                        default:
                            continue 2;
                    }
                }
                $excelData[$line] = $rowData;
                if ($error['message']) $errors[$line] = $error;
            }
            // 去掉最后一个汇总的
            if (!$excelData[$line]['invoice_no']) {
                unset($excelData[$line]);
                unset($errors[$line]);
            }
            // 验证一下数量对不对
            foreach ($excelData as $line => $rowData) {
                $search = [
                    ['invoice_no', '=', $rowData['invoice_no']],
                    ['material_code', '=', $rowData['material_code']],
                    ['expired_at', '=', ''],
                    ['case_mark', '=', $rowData['case_mark']],
                    ['branch_mark', '=', $rowData['branch_mark']],
                    ['status', '=', 2],
                ];
                $sum = ItemStock::where($search)->sum('num');
                if ($rowData['num'] != $sum) {
                    if (!isset($errors[$line])) $errors[$line] = ['line' => $line, 'message' => ''];
                    $errors[$line]['message'] .= '数量和查询数量不符，请修改或重新导出';
                }
            }
            if ($errors) return $this->sendData(200, '', $errors);
            //插入数据库
            DB::connection('labelDB')->beginTransaction();
            $count = 0;
            foreach ($excelData as $rowData) {
                $search = [
                    ['invoice_no', '=', $rowData['invoice_no']],
                    ['material_code', '=', $rowData['material_code']],
                    ['expired_at', '=', ''],
                    ['status', '=', 2],
                ];
                $updateData = [
                    'expired_at' => $rowData['expired_at'],
                    'production_date' => $rowData['production_date'],
                    'valid_month' => $rowData['valid_month'],
                ];
                $up = [
                    'valid_month' => $rowData['valid_month']
                ];
                Item::where('material_code',$rowData['material_code'])->update($up);
                ItemStock::where($search)->update($updateData);
                $count++;
            }
            $errors = [];
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::connection('labelDB')->rollBack();
            return $this->sendData(402, $e->getMessage());
        }
        if ($count > 0) $this->log(self::LOG_IMPORT, '导入' . $count . '条有效期数据');

        return $this->sendData(200, '', ['count' => $count]);
    }

    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        $queryResults = QueryResult::whereIn('id', $ids)->get();
        if (count($queryResults) == 0) return $this->sendData(402, '要删除的记录不存在');
        try {
            DB::connection('labelDB')->beginTransaction();
            foreach ($queryResults as $r) {
                $search = [
                    'status' => 2,
                    'invoice_no' => $r->invoice_no,
                    'item_no' => $r->item_no,
                    'box_mark' => $r->box_mark,
                    'case_mark' => $r->case_mark,
                    'branch_mark' => $r->branch_mark,
                    'location_no' => $r->location_no,
                    'support_no' => $r->support_no,
                    'material_code' => $r->material_code,
                    'state' => 0
                ];
                $stock = ItemStock::where($search)->first();
                if (!$stock || $stock->num < $r->num) throw new \Exception($r->item_no . '商品库存发生变化，无法删除');
                if ($stock->expired_at) throw new \Exception($r->item_no . '已导入有效期，无法删除');
                if ($stock->num == $r->num) {
                    $stock->delete();
                } else {
                    $stock->num -= $r->num;
                    $stock->save();
                }
                // 修改invoice和stage库存
                $invoice = Invoice::where(['invoice_no' => $r->invoice_no, 'item_no' => $r->item_no])->first();
                $invoice->confirm_num -= $r->num;
                $invoice->stage_num += $r->num;
                $invoice->save();
                $insert = [
                    'num' => $r->num,
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
                $this->log(self::LOG_IMPORT, '删除' . $r->item_no . '库位号' . $r->invoice_no . '制造记号' . $r->branch_mark . '共' . $r->num . '条查询数据');
                $r->delete();
            }
            DB::connection('labelDB')->commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::connection('labelDB')->rollBack();
            return $this->sendData(402, $e->getMessage());
        }

        return $this->sendData();
    }
}
