<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Label\Models\ItemStock;
use Modules\Label\Models\Outstorage;
use Excel;
use PHPExcel_IOFactory;

class OutstorageController extends BaseController
{
    // 分页获取出库单列表
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [];
        if ($request->get('task_no')) $where[] = ['task_no','like', $request->get('task_no'). '%'];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];
        if ($request->get('expired_at')) $where[] = ['expired_at', '=', $request->get('expired_at')];
        $outstorages = Outstorage::where($where)->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $outstorages);
    }

    // 分页获取可出库列表
    public function getList(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 's.updated_at';
        $db = DB::connection('labelDB');
        $where = [
            ['s.state', '=', 0],
            ['s.shopping', '=', 0]
        ];
        if ($request->get('item_no')) $where[] = ['s.item_no', '=', $request->get('item_no')];
        if ($request->get('invoice_no')) $where[] = ['s.invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('support_no')) $where[] = ['s.support_no', '=', $request->get('support_no')];
        if ($request->get('material_code')) $where[] = ['s.material_code', '=', $request->get('material_code')];
        if ($request->get('location_no')) $where[] = ['s.location_no', '=', $request->get('location_no')];
        if ($request->get('status')) $where[] = ['s.created_at', '=', $request->get('status')];
        if ($request->get('brand')) $where[] = ['i.brand', '=', $request->get('brand')];
        
        $stocks = $db->table('item_stock as s')
        ->leftJoin('items as i', 's.material_code', '=', 'i.material_code')
        ->select('s.id','s.item_no','s.invoice_no','s.material_code','i.brand','s.item_name','s.status','s.location_no','s.support_no','s.num', 's.updated_at')
        ->where($where)
        ->whereIn('status', [2,3,4])
        ->orderBy($sort, 'desc')
        ->paginate($limit);
        // dd($stocks);
        return $this->sendData(200, '', $stocks);
    }

    // 分页获取购物车列表
    public function getLists(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'updated_at';
        $where = [
            ['state', '=', 0],
            ['shopping', '=', 1]
        ];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('support_no')) $where[] = ['support_no', '=', $request->get('support_no')];
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];
        if ($request->get('location_no')) $where[] = ['location_no', '=', $request->get('location_no')];
        if ($request->get('status')) $where[] = ['created_at', '=', $request->get('status')];
        $stocks = ItemStock::where($where)->whereIn('status', [2, 3,4])->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $stocks);
    }

    public function shopping(Request $request)
    {
        $ids = $request->all();
        if ($ids && !is_array($ids)) $ids = explode(',', $ids);
        if (empty($ids)) return $this->sendData(402, '数据不能为空');
        foreach ($ids as $id) {
            $stocks = ItemStock::where('id', $id)->first();
            $stocks->shopping = 1;
            $stocks->save();
        }
        return $this->sendData();
    }



    // 执行出库
    public function submit(Request $request)
    {
        $all = $request->all();
        if (empty($all)) return $this->sendData(402, '数据不能为空');
        $ids = [];
        foreach ($all as $key) {
            $ids = $key['ids'];
        } 
            $task_no = preg_replace('# #', '', $key['code']) . date('YmdHis');
            if (empty($key['ids'])) return $this->sendData(402, '数据不能为空');
            $stocks = ItemStock::whereIn('id', $ids)->get();
            try {
                DB::connection('labelDB')->beginTransaction();
                $outstorages = $sps = [];
                foreach ($stocks as $stock) {
                    if ($stock->status == 1) throw new \Exception('stage的商品无法出库');
                    if ($stock->state != 0) throw new \Exception('移库中的商品无法出库');
                    $key = $stock->invoice_no . '_' . $stock->item_no . '_' . $stock->expired_at . '_'.$stock->marks. '_'.$stock->status;
                    if (!in_array($stock->support_no, $sps)) $sps[] = $stock->support_no;
                    if (isset($outstorages[$key])) {
                        $outstorages[$key]['total'] += $stock->num;
                    } else {
                        $outstorages[$key] = [
                            'total' => $stock->num,
                            'invoice_no' => $stock->invoice_no,
                            'item_no' => $stock->item_no,
                            'expired_at' => $stock->expired_at,
                            'material_code' => $stock->material_code,
                            'item_name' => $stock->item_name,
                            'branch_mark' => $stock->branch_mark,
                            'case_mark' => $stock->case_mark,
                            'box_mark' => $stock->box_mark,
                            'status' => $stock->status,
                            'support_no' => $stock->support_no,
                            'production_date' => $stock->production_date,
                            'valid_month' => $stock->valid_month,
                            'marks' => $stock->marks,
                            
                        ];
                    }
                    $stock->delete();
                }
                $count = ItemStock::whereIn('support_no', $sps)->count();
                if ($count > 0) throw new \Exception('出库的托盘存在其他商品，请先移库');
                foreach ($outstorages as $item) {
                    $insert = [
                        'task_no' => $task_no,
                        'item_no' => $item['item_no'],
                        'expired_at' => $item['expired_at'] ? $item['expired_at'] : '',
                        'invoice_no' => $item['invoice_no'],
                        'material_code' => $item['material_code'],
                        'item_name' => $item['item_name'],
                        'status' => $item['status'],
                        'user_id' => $this->user->id,
                        'num' => $item['total'],
                        'box_mark' => $item['box_mark'],
                        'case_mark' => $item['case_mark'],
                        'branch_mark' => $item['branch_mark'],
                        'production_date' => $item['production_date'],
                        'support_no' => $item['support_no'],
                        'valid_month' => $item['valid_month'],
                        'marks' => $item['marks']
                    ];
                    Outstorage::create($insert);
                }
                DB::connection('labelDB')->commit();
            } catch (\Exception $e) {
                DB::connection('labelDB')->rollBack();
                Log::info($e->getMessage());
                return $this->sendData(402, $e->getMessage());
            }
            $this->log(self::LOG_OUTSTORAGE, '单号' . $task_no . '出库');
        return $this->sendData();
    }

    // 根据任务编号获取任务
    public function getByNo(Request $request)
    {
        $task_no = $request->get('task_no');
        if (!$task_no || !$task = Outstorage::where('task_no', $task_no)->get()) {
            return $this->sendData(402, '单号错误');
        }

        return $this->sendData(200, '', $task);
    }

    public function del(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) return $this->sendData(402, '数据不能为空');
        foreach ($ids as $key) {
            $stocks = ItemStock::where('id', $key)->first();
            $stocks->shopping = '0';
            $stocks->save();
        }
        return $this->sendData();
    }

    public function export(Request $request)
    {
        $task_no = $request->get('task_no');
        // $outstorage = Outstorage::where('task_no',$task_no)->get();
        $outstorage = Outstorage::where('task_no', $task_no)->with('item')->orderBy('support_no', 'asc')->get()->toArray();
        // $fileName = str_replace('/', '-', $task_no) . '总查询制造记号';
        $flag = [];
        foreach($outstorage as $out){
            if(!$out['item']){
                $flag[] = $out['material_code'];
                continue;
            }
        }
        if($flag){
            $i = implode(",", $flag);
            return $i . '没有填写基础信息';
        }
        $fileName = date('YmdHis');
        return Excel::create($fileName, function ($excel) use ($task_no, $outstorage) {
            $excel->setTitle('总表');
            $excel->sheet('总表', function ($sheet) use ($outstorage) {
                $data = [
                    ['发票号', '托号', '料号', '品名', '最小', '箱数', '中盒', '最小单位', '效期', '备注'],
                    ['', '', '', '', "制造记号3", '', '', '', '', ''],
                    [],
                ];
                foreach ($outstorage as $list) {
                    
                    if (!$list['item']['case_num'] || $list['item']['case_num'] == '999' || $list['item']['case_num'] == '999999') {
                        $case_num = '/';
                    } else {
                        $case_num = ceil($list['num'] / $list['item']['case_num']);
                    }
                    if (!$list['item']['box_num'] || $list['item']['box_num'] == '999' || $list['item']['box_num'] == '999999') {
                        $box_num = '/';
                    } else {
                        $box_num = ceil($list['num'] / $list['item']['box_num']);
                    }
                    if(!$list['expired_at']){
                        $list['expired_at'] = '/';
                    }
                    $data[] = [
                        $list['invoice_no'],
                        $list['support_no'],
                        $list['item_no'],
                        $list['item']['name'],
                        $list['branch_mark'] ? $list['branch_mark'] : '/',
                        $box_num,
                        $case_num,
                        $list['num'],
                        str_replace('-', '',$list['expired_at']),
                        $list['marks'],
                    ];
                }
            
                $sheet->fromArray($data, null, 'A1', false, false);
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  11,
                        'bold'      =>  false
                    ),
                ));
                $sheet->row(1, function ($row) {
                    $row->setFontSize('12');
                    $row->setFontFamily('微软雅黑');
                });
                $sheet->row(2, function ($row) {
                    $row->setFontSize('12');
                    $row->setFontFamily('微软雅黑');
                });
                $style_array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle('A1:J' . (count($outstorage) + 3))->applyFromArray($style_array);
                $sheet->setWidth('A', 8);
                $sheet->setWidth('B', 11);
                $sheet->setWidth('C', 10);
                $sheet->setWidth('D', 30);
                $sheet->setWidth('E', 11);
                $sheet->setWidth('F', 11);
                $sheet->setWidth('G', 11);
                $sheet->setWidth('H', 10);
                $sheet->setWidth('I', 10);
                $sheet->setWidth('J', 8);
                $sheet->mergeCells('A1:A3');
                $sheet->mergeCells('B1:B3');
                $sheet->mergeCells('C1:C3');
                $sheet->mergeCells('D1:D3');
                $sheet->mergeCells('E2:E3');
                $sheet->mergeCells('F1:F3');
                $sheet->mergeCells('G1:G3');
                $sheet->mergeCells('H1:H3');
                $sheet->mergeCells('I1:I3');
                $sheet->mergeCells('J1:J3');
                $sheet->cells('A1:J' . (count($outstorage) + 3), function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
            $pre = $prf = [];
            $pre_key = '';
            foreach ($outstorage as $k => $info) {
                $key = $info['support_no'];
                if ($k === 0) {
                    $pre[$key] = $info;
                    $pre_key = $key;
                    continue;
                }
                if (array_key_exists($key, $pre)) {
                    $pre[$key]['num'] += $info['num'];
                } else {
                    $prf[] = $pre[$pre_key];
                    $pre = [];
                    $pre_key = $key;
                    $pre[$key] = $info;
                }
            }
            //  最后一条特殊处理
            $prf[] = $pre[$pre_key];
            // dd($prf);
            $limit = ceil(count($prf) / '20');
            // if($limit <= 1){
            //     $result = Outstorage::where('task_no', $task_no)->with('item')->orderBy('support_no', 'asc')->get()->toArray();
            //     $title = 1;
            //     $excel->setTitle("$title");
            //     $excel->sheet("$title", function ($sheet) use ($result,$title) {
            //         dd($result);
            //      $data = [
            //          ['资生堂发货单','','','','','','','','',"$title"],
            //          ['交接日期'],
            //          ['发票号','托号','料号','品名','最小','箱数','中盒','最小单位','效期','备注'],
            //      ];
            //      $total = 0;
            //      foreach($result as $val){
            //          if (!$val['item']['case_num'] || $val['item']['case_num'] == '999' || $val['item']['case_num'] == '999999') {
            //              $case_num = '/';
            //          } else {
            //              $case_num = ceil($val['num'] / $val['item']['case_num']);
            //          }
            //          if (!$val['item']['box_num'] || $val['item']['box_num'] == '999' || $val['item']['box_num'] == '999999') {
            //              $box_num = '/';
            //          } else {
            //              $box_num = ceil($val['num'] / $val['item']['box_num']);
            //          }
            //          $stockRow = [
            //              $val['invoice_no'],
            //              $val['support_no'],
            //              $val['item_no'],
            //              $val['item_name'],
            //              $val['branch_mark'],
            //              $box_num,
            //              $case_num,
            //              $val['num'],
            //              str_replace('-', '',$val['expired_at']),
            //              '',
            //          ];
            //          $data[]  = $stockRow;
            //          $len[] = $stockRow;
            //          $support[] = $val['support_no'];
            //          $total += $box_num;
            //      }
            //      $tuo = count(array_flip($support));
            //      $data[] =['','','','备注：','',"$tuo",'托',"$total",'箱','']; 
            //      $data[] =['','','','','预计到达时间','','','','','']; 
            //      $data[] =['运输车辆信息：','','车牌号：','','联系人：','','电话','','','']; 
            //      $data[] =['','','','木托盘1.1*1.1','横杆','','','','','']; 
            //      $data[] =['','','','','','','','','','']; 
            //      $data[] =['仓库：','','质检复核：','','','承运人：','','收货人：','',''];
            //      $data[] =['','','','','','','','','',''];  
            //      $data[] =['封号：','','','','','','','','',''];  
            //      $data[] =['日期：','','日期：','','','日期：','','日期：','',''];  
            //      $sheet->fromArray($data, null, 'A1', false, false);
            //      $sheet->setWidth('A', 8);
            //      $sheet->setWidth('B', 11);
            //      $sheet->setWidth('C', 10);
            //      $sheet->setWidth('D', 30);
            //      $sheet->setWidth('E', 11);
            //      $sheet->setWidth('F', 11);
            //      $sheet->setWidth('G', 11);
            //      $sheet->setWidth('H', 10);
            //      $sheet->setWidth('I', 10);
            //      $sheet->setWidth('J', 8);
            //      $sheet->mergeCells('A1:I1');
            //      $sheet->mergeCells('A2:I2');
            //      $sheet->mergeCells('J1:J2');
            //      $style_array = array(
            //          'borders' => array(
            //              'allborders' => array(
            //                  'style' => \PHPExcel_Style_Border::BORDER_THIN
            //              )
            //          )
            //      );
            //      $A = count($len)+7;
            //      $E = count($len)+8;
            //      $sheet->getStyle('A1:J' . (count($len)+3))->applyFromArray($style_array);
            //      $sheet->getStyle('A'."$A".':'.'E'.$E)->applyFromArray($style_array);
            //      $sheet->cells('A1:J' . (count($data) ), function ($cells) {
            //          $cells->setAlignment('center');
            //          $cells->setValignment('center');
            //      });
            //  });
            // }else{
                for($i=0; $i<$limit; $i++){
                    if($i == 0){
                        $start = 0;
                    }
                    if($i == 1){
                        $res = array_slice($prf,$start,20);
                    }
                    $res = array_slice($prf,$start,20);
                    $tmp[] =[
                        'start'=>current($res)['support_no'],
                        'end'=>end($res)['support_no'],
                    ];
                    $start += 20;
                }
                foreach($tmp as $v=>$key){
                   $result = Outstorage::where('task_no', $task_no)->where('support_no','>=',$key['start'])->where('support_no','<=',$key['end'])->with('item')->orderBy('support_no', 'asc')->get()->toArray();
                   if($v == 0){
                    $title = 0;
                   }
                   $title += 1;
                   $excel->setTitle("$title");
                   $excel->sheet("$title", function ($sheet) use ($result,$title) {
                    
                    $total = 0;
                    foreach($result as $val){
                        $created_at = $val['created_at'];
                        $invoice_no[] = [
                            $val['invoice_no']
                        ];
                        if (!$val['item']['case_num'] || $val['item']['case_num'] == '999' || $val['item']['case_num'] == '999999') {
                            $case_num = '/';
                        } else {
                            $case_num = ceil($val['num'] / $val['item']['case_num']);
                        }
                        if (!$val['item']['box_num'] || $val['item']['box_num'] == '999' || $val['item']['box_num'] == '999999') {
                            $box_num = '/';
                        } else {
                            $box_num = ceil($val['num'] / $val['item']['box_num']);
                        }
                        if(!$val['expired_at']){
                            $val['expired_at'] = '/';
                        }
                        $stockRow = [
                            $val['invoice_no'],
                            $val['support_no'],
                            $val['item_no'],
                            $val['item']['name'],
                            $val['branch_mark'] ? $val['branch_mark'] : '/',
                            $box_num,
                            $case_num,
                            $val['num'],
                            str_replace('-', '',$val['expired_at']),
                            $val['marks'],
                        ];
                        $data[]  = $stockRow;
                        $len[] = $stockRow;
                        $support[] = $val['support_no'];
                        if($box_num == '/'){
                            continue;
                        }
                        $total += $box_num;
                    }
                    $ids = array_column($invoice_no,'0');
                    $invoice_nos = array_unique($ids);
                    $danhao = implode('、',$invoice_nos);
                    $md = strtotime($created_at);
                    $mds = date("m/d",$md);
                    $datas =['资生堂发货单','','','','','','','','',"$title"];
                    $dataj =["交接日期:$mds"."发票号：$danhao"];
                    $dataf =['发票号','托号','料号','品名','最小','箱数','中盒','最小单位','效期','备注'];
                    array_unshift($data,$dataf);
                    array_unshift($data,$dataj);
                    array_unshift($data,$datas);
                    $tuo = count(array_flip($support));
                    $data[] =['','','','备注：','',"$tuo",'托',"$total",'箱','']; 
                    $data[] =['','','','','预计到达时间','','','','','']; 
                    $data[] =['运输车辆信息：','','车牌号：','','联系人：','','电话','','','']; 
                    $data[] =['','','','木托盘1.1*1.1','横杆','','','','','']; 
                    $data[] =['','','','','','','','','','']; 
                    $data[] =['仓库：','','质检复核：','','','承运人：','','收货人：','',''];
                    $data[] =['','','','','','','','','',''];  
                    $data[] =['封号：','','','','','','','','',''];  
                    $data[] =['日期：','','日期：','','','日期：','','日期：','',''];  
                    $sheet->fromArray($data, null, 'A1', false, false);
                    $sheet->mergeCells('A1:I1');
                    $sheet->mergeCells('A2:I2');
                    $sheet->mergeCells('J1:J2');
                    $style_array = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
                    $styleThinBlackBorderOutlines = array(
                        'borders' => array(
                            'bottom' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN, //设置border样式 'color' => array ('argb' => 'FF000000'), //设置border颜色
                            ),
                        ),);
                    $F = count($len)+4;
                    $A = count($len)+7;
                    $E = count($len)+8;
                    $sheet->getStyle('F'."$F".':'.'F'.$F)->applyFromArray($styleThinBlackBorderOutlines);
                    $sheet->getStyle('H'."$F".':'.'H'.$F)->applyFromArray($styleThinBlackBorderOutlines);
                    $sheet->getStyle('A1:J' . (count($len)+3))->applyFromArray($style_array);
                    $sheet->getStyle('A'."$A".':'.'E'.$E)->applyFromArray($style_array);
                    $sheet->cells('A1:J' . (count($data) ), function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                });
                }
            // }
            
            $result = Outstorage::where('task_no', $task_no)->with('item')->orderBy('support_no', 'asc')->get()->toArray();
            $excel->sheet('制造记号', function ($sheet) use ($result,$title) {
             $data = [
                 ['序号','发票号','产品编号','系统代码','SAP代码','产品名称','制造记号',"最小单位\r\n制造记号",'赏味日期','箱型','产品','','','','','','','','差异说明','','','',"登记\r\n入库时\r\n间",'','',''],
                 ['','','','','','','','','','','数量','','','','','全量',"良品在\r\n库",'抽样','取样','进口原因','','','登记入库时间','','',''],
                 ['','','','','','','','','','',"总\r\n箱数",'栈板数','箱入数','盒入数','支数','','','','',"进口\r\n不良","进口\r\n差异",'备注','','生产年月','保质期','图片编号'],
             ];
             foreach($result as $key=> $val){
                 if (!$val['item']['case_num'] || $val['item']['case_num'] == '999' || $val['item']['case_num'] == '999999') {
                     $case_num = '/';
                     $val['item']['case_num'] = '/';
                 } else {
                     $case_num = ceil($val['num'] / $val['item']['case_num']);
                 }
                 if (!$val['item']['box_num'] || $val['item']['box_num'] == '999' || $val['item']['box_num'] == '999999') {
                     $box_num = '/';
                     $val['item']['box_num'] = '/';
                 } else {
                     $box_num = ceil($val['num'] / $val['item']['box_num']);
                 }
                 if(!$val['valid_month'] || $val['valid_month'] == '999' || $val['valid_month'] == '999999'){
                    $val['valid_month'] = '/';
                 }
                 if($val['expired_at']){
                    $val['expired_at'] = str_replace('-', '年',$val['expired_at']).'月';
                 }else{
                    $val['expired_at'] ='/';
                 }
                 if($val['production_date']){
                    $val['production_date'] = str_replace('-', '年',$val['production_date']).'月';
                 }else{
                    $val['production_date'] = '/';
                 }
                 $data[] = [
                     $key+1,
                     $val['invoice_no'],
                     $val['item_no'],
                     $val['item_no'],
                     $val['material_code'],
                     $val['item']['name'],
                     $val['branch_mark'] ? $val['branch_mark'] : '/',
                     $val['branch_mark'] ? $val['branch_mark'] : '/',
                     $val['expired_at'] ,
                     '',
                     $box_num,
                     '',
                     $val['item']['box_num'] ? $val['item']['box_num'] : '/',
                     $val['item']['case_num'] ? $val['item']['case_num'] : '/',
                     $val['num'],
                     $val['num'],
                     '',
                     '',
                     '',
                     '',
                     '',
                     '',
                     '',
                     $val['production_date'],
                     $val['valid_month'],
                     '',
                 ];
             }
             $sheet->fromArray($data, null, 'A1', false, false);
             $sheet->setStyle(array(
                'font' => array(
                    'name'      =>  'Calibri',
                    'size'      =>  11,
                    'bold'      =>  false
                ),
            ));
            $sheet->row(1, function ($row) {
                $row->setFontWeight('bold');
                $row->setFontSize('12');
                $row->setFontFamily('微软雅黑');
            });
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
           
            // 设置样式
            $style_array = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            
            $sheet->getStyle('A1:Z' . (count($data)))->applyFromArray($style_array);
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
             $sheet->mergeCells('A1:A3');
             $sheet->mergeCells('B1:B3');
             $sheet->mergeCells('C1:C3');
             $sheet->mergeCells('D1:D3');
             $sheet->mergeCells('E1:E3');
             $sheet->mergeCells('F1:F3');
             $sheet->mergeCells('G1:G3');
             $sheet->mergeCells('H1:H3');
             $sheet->mergeCells('I1:I3');
             $sheet->mergeCells('J1:J3');
             $sheet->mergeCells('K1:R1');
             $sheet->mergeCells('K2:O2');
             $sheet->mergeCells('P2:P3');
             $sheet->mergeCells('Q2:Q3');
             $sheet->mergeCells('R2:R3');
             $sheet->mergeCells('S2:S3');
             $sheet->mergeCells('S1:U1');
             $sheet->mergeCells('T2:U2');
             $sheet->mergeCells('W1:W3');
             $style_array = array(
                 'borders' => array(
                     'allborders' => array(
                         'style' => \PHPExcel_Style_Border::BORDER_THIN
                     )
                 )
             );
             $sheet->getStyle('A1:Z' . (count($data)))->applyFromArray($style_array);
             $sheet->cells('A1:Z' . (count($data)), function ($cells) {
                 $cells->setAlignment('center');
                 $cells->setValignment('center');
             });
         });
        })->export('xls');
    }
}
