<?php

namespace Modules\Label\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Label\Jobs\InoviceImport;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Modules\Label\Models\Invoice;
use PHPExcel_IOFactory;
use Modules\Label\Utils\Import;

class InvoiceController extends BaseController
{
    // 分页获取发票清单
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'arrivetime';
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['invoice_no', 'like', '%'. $request->get('invoice_no'). '%'];
        if ($request->get('item_no')) $where[] = ['item_no', '=', $request->get('item_no')];
        if ($request->get('material_code')) $where[] = ['material_code', '=', $request->get('material_code')];
        if ($request->get('stocktime')) $where[] = ['stocktime', '=', $request->get('stocktime')];
        $invoices = Invoice::where($where)->with('item')->orderBy($sort, 'desc')->paginate($limit);

        return $this->sendData(200, '', $invoices);
    }

    // 分页获取商检清单
    public function unionIndex(Request $request)
    {
        $db = DB::connection('labelDB');
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $where = [];
        if ($request->get('invoice_no')) $where[] = ['c.invoice_no', 'like', '%' . $request->get('invoice_no'). '%'];
        if ($request->get('material_code')) $where[] = ['i.material_code', 'like', '%' . $request->get('material_code')];
        if ($request->get('item_no')) $where[] = ['c.item_no', '=', $request->get('item_no')];
        if ($request->get('inspection_no')) $where[] = ['c.inspection_no', 'like', '%' . $request->get('inspection_no')];
        if ($request->get('customs_no')) $where[] = ['c.customs_no', 'like', '%' . $request->get('customs_no')];
        if ($request->get('quarantine_no')) $where[] = ['c.quarantine_no', '=', $request->get('quarantine_no')];
        if ($request->get('certificate_time')) $where[] = ['c.certificate_time', '=', $request->get('certificate_time')];
        // if ($request->get('stocktime')) $where[] = ['i.stocktime', '=', $request->get('stocktime')];
        $data = $db->table('commodity_inspection as c')
            ->leftJoin('invoices as i', 'c.invoice_id', '=', 'i.id')
            ->select('i.brand_desc', 'c.invoice_no', 'i.produce_line_desc', 'c.id', 'i.material_code', 'c.item_no', 'c.material_desc', 'c.number', 'c.inspection_no', 'c.customs_no', 'c.quarantine_no', 'c.certificate_time')
            ->where($where)
            ->orderBy('c.inspection_no', 'desc')
            ->paginate($limit);
        return $this->sendData(200, '', $data);
    }
     // 分页获取海关清单
     public function unionIndexs(Request $request)
     {
         $db = DB::connection('labelDB');
         $limit = $request->get('limit') ? $request->get('limit') : 20;
         $where = [];
         if ($request->get('invoice_no')) $where[] = ['c.invoice_no', 'like', '%' . $request->get('invoice_no')];
         if ($request->get('material_code')) $where[] = ['i.material_code', 'like', '%' . $request->get('material_code')];
         if ($request->get('item_no')) $where[] = ['c.item_no', '=', $request->get('item_no')];
         if ($request->get('inspection_no')) $where[] = ['c.inspection_no', 'like', '%' . $request->get('inspection_no')];
         if ($request->get('customs_no')) $where[] = ['c.customs_no', 'like', '%' . $request->get('customs_no')];
         if ($request->get('quarantine_no')) $where[] = ['c.quarantine_no', '=', $request->get('quarantine_no')];
         if ($request->get('certificate_time')) $where[] = ['c.certificate_time', '=', $request->get('certificate_time')];
         $data = $db->table('commodity_inspection as c')
             ->leftJoin('invoices as i', 'c.invoice_id', '=', 'i.id')
             ->select('i.brand_desc', 'c.invoice_no', 'i.produce_line_desc', 'c.id', 'i.material_code', 'c.item_no', 'c.material_desc', 'c.number', 'c.inspection_no', 'c.customs_no', 'c.quarantine_no', 'c.certificate_time','c.status')
             ->where($where)
             ->orderBy('c.inspection_no', 'desc')
             ->paginate($limit);
 
         return $this->sendData(200, '', $data);
     }
    // 发票清单导入
    public function import(Request $request)
    {
        // dd(Redis::del('invoice_import_dealing_' . $this->user->id));
        if (Redis::get('invoice_import_dealing_' . $this->user->id)) return $this->sendData(402, '文件正在导入中，请稍后再上传');
        if (!$request->hasFile('file')) return $this->sendData(402, '上传失败,检查文件格式');
        $path = $this->saveFile($request->file('file'));
        Redis::set('invoice_import_path_' . $this->user->id, $path);
        Redis::set('invoice_import_dealing_' . $this->user->id, 1);
        InoviceImport::dispatch();

        return $this->sendData();
    }
    public function downloadTpl(Request $request)
    {
        $import = new Import('', 'invoices_in', true);
        return $import->downloadTpl();
    }

    // 获取导入报错信息
    public function getImportResult()
    {
        $count = Redis::get('invoice_import_count_' . $this->user->id);
        $errors = json_decode(Redis::get('invoice_import_errors_' . $this->user->id), true);
        $result = [
            'dealing' => 0,
            'count' => $count ? $count : 0,
            'errors' => $errors ? $errors : [],
        ];
        if (Redis::get('invoice_import_dealing_' . $this->user->id)) {
            $result['dealing'] = 1;
        }

        return $this->sendData(200, '', $result);
    }

    //根据ID获取一条数据
    public function getById(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return $this->sendData(402, 'ID不能为空');
        return $this->sendData(200, '', Invoice::find($id));
    }

    //根据ID获取库存
    public function getByIds(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)) return $this->sendData(402, 'ID不能为空');
        if (!is_array($ids)) return $this->sendData(402, 'ID必须是一个数组');
        $invoices = Invoice::whereIn('id', $ids)->get();

        return $this->sendData(200, '', $invoices);
    }

    // 删除
    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        if (!$ids) return $this->sendData(402, 'ID不能为空');
        $invoices = Invoice::whereIn('id', $ids)->get();
        if (count($invoices) == 0) return $this->sendData(402, 'ID错误');
        // 如果已上架，就不能删除
        foreach ($invoices as $invoice) {
            if ($invoice->status == 0) {
                $invoice->delete();
            } else {
                $invoice->num = 0;
                $invoice->save();
            }
        }
        $this->log(self::LOG_OTHER, '删除发票明细');

        return $this->sendData();
    }

    //修改发票清单
    public function save(Request $request)
    {
        $params = $request->all();
        if (empty($params['invoice_no'])) return $this->sendData(402, '发票号不能为空');
        if (empty($params['sap_no'])) return $this->sendData(402, '内向交货单号不能为空');
        if (empty($params['arrivetime']) || !strtotime($params['arrivetime'])) {
            return $this->sendData(402, '预计到港日格式错误');
        }
        if (empty($params['material_code'])) return $this->sendData(402, '物料代码不能为空');
        if (empty($params['item_no'])) return $this->sendData(402, '进口代码不能为空');
        if (empty($params['material_desc'])) return $this->sendData(402, '物料描述(产品名)不能为空');
        if (empty($params['num']) || $params['num'] <= 0) return $this->sendData(402, '数量不能小于0');
        if (empty($params['intransit_num'])) $params['intransit_num'] = 0;
        if (empty($params['prepare_num'])) $params['prepare_num'] = 0;
        if (empty($params['stock_num'])) $params['stock_num'] = 0;
        if (empty($params['diff_num'])) $params['diff_num'] = 0;
        if (empty($params['other_num'])) $params['other_num'] = 0;
        if (empty($params['purchase_num'])) $params['purchase_num'] = 0;
        if (empty($params['provider_code'])) $params['provider_code'] = '';
        if (empty($params['purchase_no'])) $params['purchase_no'] = '';
        if (empty($params['brand'])) $params['brand'] = '';
        if (empty($params['brand_desc'])) $params['brand_desc'] = '';
        if (empty($params['produce_line'])) $params['produce_line'] = '';
        if (empty($params['produce_line_desc'])) $params['produce_line_desc'] = '';
        if (empty($params['purchase_group'])) $params['purchase_group'] = '';
        if (empty($params['purchase_group_desc'])) $params['purchase_group_desc'] = '';
        $params['arrivetime'] = date('Y-m-d', strtotime($params['arrivetime']));
        $search = [
            'item_no' => $params['item_no'],
            'invoice_no' => $params['invoice_no'],
            'material_code' => $params['material_code'],
        ];
        $exist = Invoice::where($search)->first();
        if (isset($params['id']) && $invoice = Invoice::find($params['id'])) {
            if ($exist && $exist->id != $params['id']) return $this->sendData(402, '商品不能重复');
            $invoice->sap_no = $params['sap_no'];
            $invoice->arrivetime = $params['arrivetime'];
            $invoice->factory_code = $params['factory_code'];
            $invoice->num = $params['num'];
            $invoice->intransit_num = $params['intransit_num'];
            $invoice->prepare_num = $params['prepare_num'];
            $invoice->stock_num = $params['stock_num'];
            $invoice->diff_num = $params['diff_num'];
            $invoice->other_num = $params['other_num'];
            $invoice->provider_code = $params['provider_code'];
            $invoice->purchase_no = $params['purchase_no'];
            $invoice->purchase_num = $params['purchase_num'];
            $invoice->brand = $params['brand'];
            $invoice->brand_desc = $params['brand_desc'];
            $invoice->produce_line = $params['produce_line'];
            $invoice->produce_line_desc = $params['produce_line_desc'];
            $invoice->purchase_group = $params['purchase_group'];
            $invoice->purchase_group_desc = $params['purchase_group_desc'];
            $invoice->save();
            $this->log(self::LOG_OTHER, '修改发票清单发票号' . $params['invoice_no'] . '商品' . $params['material_code']);
        } else {
            if ($exist) return $this->sendData(402, '商品不能重复');
            Invoice::create([
                'invoice_no' => $params['invoice_no'],
                'sap_no' => $params['sap_no'],
                'arrivetime' => $params['arrivetime'],
                'material_code' => $params['material_code'],
                'item_no' => $params['item_no'],
                'material_desc' => $params['material_desc'],
                'factory_code' => $params['factory_code'],
                'num' => $params['num'],
                'intransit_num' => $params['intransit_num'],
                'prepare_num' => $params['prepare_num'],
                'stock_num' => $params['stock_num'],
                'diff_num' => $params['diff_num'],
                'other_num' => $params['other_num'],
                'provider_code' => $params['provider_code'],
                'purchase_no' => $params['purchase_no'],
                'purchase_num' => $params['purchase_num'],
                'brand' => $params['brand'],
                'brand_desc' => $params['brand_desc'],
                'produce_line' => $params['produce_line'],
                'produce_line_desc' => $params['produce_line_desc'],
                'purchase_group' => $params['purchase_group'],
                'purchase_group_desc' => $params['purchase_group_desc'],
            ]);
            $this->log(self::LOG_OTHER, '新增发票清单发票号' . $params['invoice_no'] . '商品' . $params['material_code']);
        }

        return $this->sendData();
    }

    //商检清单导入
    public function importInspection(Request $request)
    {
        if (!$request->hasFile('file')) return $this->sendData(402, '上传失败!');
        $path = $this->saveFile($request->file('file'));
        try {
            // 开启事务
            DB::beginTransaction();
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $name = str_replace('、', '/', $originalName);
            $pos = strpos($name, '&');
            if (!$pos) {
                $replace = preg_replace('/([\x80-\xff]*)/i', '', $name);
                $str = str_replace(strrchr($replace, '.'), '', $replace);
                $app = preg_replace('/\(.*?\)/', '', $str);
                $res = explode('&', $app);
            } else {
                $replace = preg_replace('/([\x80-\xff]*)/i', '', $name);
                $str = str_replace(strrchr($replace, '.'), '', $replace);
                $res = explode('&', $str);
            }
            $db = DB::connection('labelDB');
            $inputFileType = PHPExcel_IOFactory::identify($path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($path);
            $sheet = $objPHPExcel->getSheet(0);
            $data = [];
            $highestRow = $sheet->getHighestRow();
            for ($i = 1; $i <= $highestRow; $i++) {
                if ($sheet->getCell('A' . $i)->getValue() == null) {
                    $j = $i - 1;
                    $m = $i + 1;
                    $baojian = $sheet->getCell("D" . $j)->getValue();
                    $baoguan = $sheet->getCell("M" . $m)->getValue();
                    continue;
                } else {
                    if (!$baojian) {
                        return $this->sendData(402, '上传失败，报检单号不存在');
                    }
                    if (!$baoguan) {
                        return $this->sendData(402, '上传失败，报关单号不存在');
                    }
                    if(gettype($baoguan) != 'string'){
                        return $this->sendData(402, '上传失败，报关单号格式不正确');
                    }
                    $bg = json_decode($baoguan,false, 512, JSON_BIGINT_AS_STRING);
                    $data[$i]['item_no'] = (string) $sheet->getCell("B" . $i)->getValue();
                    $data[$i]['material_desc'] = $sheet->getCell("C" . $i)->getValue();
                    $data[$i]['number'] = (int) $sheet->getCell("J" . $i)->getValue();
                    $data[$i]['temporary_record'] = $sheet->getCell("D" . $i)->getValue();
                    $data[$i]['formal_record'] = $sheet->getCell("E" . $i)->getValue();
                    $data[$i]['place_of_rigin'] = $sheet->getCell("F" . $i)->getValue();
                    $data[$i]['type'] = $sheet->getCell("G" . $i)->getValue();
                    $data[$i]['weight'] = $sheet->getCell("H" . $i)->getValue();
                    $data[$i]['money'] = $sheet->getCell("I" . $i)->getValue();
                    $data[$i]['unit'] = $sheet->getCell("K" . $i)->getValue();
                    $data[$i]['specifications'] = $sheet->getCell("L" . $i)->getValue();
                    $data[$i]['customs_no'] = $bg;
                    $data[$i]['inspection_no'] = $baojian;
                    $data[$i]['created_at'] = date('Y-m-d H:i:s');
                    $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                }
            }
            $imp = $this->importcheck($res, $data);
            if ($imp[0]['type'] && $imp[0]['type'] == 1 || $imp[0]['type'] && $imp[0]['type'] == 2 || $imp[0]['type'] && $imp[0]['type'] == 3) {
                return $this->sendData(200, '导入失败', $imp);
            }
            foreach ($imp as $key => $item) {
                $tag[$key]['item_no'] = $item['item_no'];
                $tag[$key]['number'] = $item['number'];
                $tag[$key]['inspection_no'] = $item['inspection_no'];
                $tag[$key]['customs_no'] = $item['customs_no'];
                $tag[$key]['created_at'] = date('Y-m-d H:i:s');
            }
            $db->table('commodity_inspection_tag')->insert($tag);
            $db->table('commodity_inspection')->insert($imp);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $this->sendData(200, '成功', '成功');
    }

    //商检清单校验
    public function importcheck($res, $data)
    {
        $resule = [];
        $db = DB::connection('labelDB');
        $invoices = $db->table('invoices')->whereIn('invoice_no', $res)->orderBy('item_no', 'asc')->get()->toArray();
        if (!$invoices) {
            $resule[] = [
                'type' => 3,
                'invoice_no' => '发票清单未导入',
            ];
            return $resule;
        }
        //给data排序
        $last_names = array_column($data, 'item_no');
        array_multisort($last_names, SORT_ASC, $data);
        $pre = $prf = [];
        $pre_key = '';
        foreach ($invoices as $k => $info) {
            $key = $info->item_no;
            if ($k === 0) {
                $pre[$key] = $info;
                $pre_key = $key;
                continue;
            }
            if (array_key_exists($key, $pre)) {
                $pre[$key]->num += $info->num;
            } else {
                $prf[] = $pre[$pre_key];
                $pre = [];
                $pre_key = $key;
                $pre[$key] = $info;
            }
        }
        //  最后一条特殊处理
        $prf[] = $pre[$pre_key];
        $check = $lock = [];
        $check_key = '';
        foreach ($data as $k => $info) {
            $key = $info['item_no'];
            if ($k === 0) {
                $check[$key] = $info;
                $check_key = $key;
                continue;
            }
            if (array_key_exists($key, $check)) {
                $check[$key]['number'] += $info['number'];
            } else {
                $lock[] = $check[$check_key];
                $check = [];
                $check_key = $key;
                $check[$key] = $info;
            }
        }
        //最后一条特殊处理
        $lock[] = $check[$check_key];

        $flag = [];
        foreach ($prf as $key) {
            $k = $key->item_no . '-' . $key->num;
            $flag[$k] = $key;
        }
        $item = [];
        foreach ($lock as $key) {
            $k = $key['item_no'] . '-' . $key['number'];
            $item[$k] = $key;
        }
        $invoice = $db->table('invoices')->whereIn('invoice_no', $res)->orderBy('item_no', 'asc')->get();

        $push = [];
        foreach ($item as $key => $val) {
            if (array_key_exists($key, $flag)) {
                foreach ($invoice as $e => $k) {
                    foreach ($data as $d => $i) {
                        if ($i['item_no'] == $k->item_no) {
                            $munber = $i['number'] - $k->num;
                            if ($munber < 0) {
                                $k->num -= $i['number'];
                                $push[] = [
                                    'item_no' => $i['item_no'],
                                    'material_desc' => $i['material_desc'],
                                    'number' => $i['number'],
                                    'temporary_record' => $i['temporary_record'],
                                    'formal_record' => $i['formal_record'],
                                    'place_of_rigin' => $i['place_of_rigin'],
                                    'type' => $i['type'],
                                    'weight' => $i['weight'],
                                    'money' => $i['money'],
                                    'unit' => $i['unit'],
                                    'specifications' => $i['specifications'],
                                    'customs_no' => $i['customs_no'],
                                    'inspection_no' => $i['inspection_no'],
                                    'created_at' => $i['created_at'],
                                    'updated_at' => $i['updated_at'],
                                    'invoice_no' => $k->invoice_no,
                                    'invoice_id' => $k->id,
                                ];
                                unset($data[$d]);
                                continue;
                            } elseif ($munber == 0) {
                                $push[] = [
                                    'item_no' => $i['item_no'],
                                    'material_desc' => $i['material_desc'],
                                    'number' => $i['number'],
                                    'temporary_record' => $i['temporary_record'],
                                    'formal_record' => $i['formal_record'],
                                    'place_of_rigin' => $i['place_of_rigin'],
                                    'type' => $i['type'],
                                    'weight' => $i['weight'],
                                    'money' => $i['money'],
                                    'unit' => $i['unit'],
                                    'specifications' => $i['specifications'],
                                    'customs_no' => $i['customs_no'],
                                    'inspection_no' => $i['inspection_no'],
                                    'created_at' => $i['created_at'],
                                    'updated_at' => $i['updated_at'],
                                    'invoice_no' => $k->invoice_no,
                                    'invoice_id' => $k->id,
                                ];
                                unset($invoice[$e]);
                                continue;
                            } else {
                                $i['number'] -= $k->num;
                                $push[] = [
                                    'item_no' => $i['item_no'],
                                    'material_desc' => $i['material_desc'],
                                    'number' => $k->num,
                                    'temporary_record' => $i['temporary_record'],
                                    'formal_record' => $i['formal_record'],
                                    'place_of_rigin' => $i['place_of_rigin'],
                                    'type' => $i['type'],
                                    'weight' => $i['weight'],
                                    'money' => $i['money'],
                                    'unit' => $i['unit'],
                                    'specifications' => $i['specifications'],
                                    'customs_no' => $i['customs_no'],
                                    'inspection_no' => $i['inspection_no'],
                                    'created_at' => $i['created_at'],
                                    'updated_at' => $i['updated_at'],
                                    'invoice_no' => $k->invoice_no,
                                    'invoice_id' => $k->id,
                                ];
                                unset($invoice[$e]);
                                continue;
                            }
                        }
                    }
                }
            } else {
                $r = $db->table('invoices')->whereIn('invoice_no', $res)->where('item_no', $val['item_no'])->get()->toArray();
                if (!$r) {
                    $resule[] = [
                        'type' => 1,
                        'item_no' => $val['item_no'],
                    ];
                    continue;
                }
                foreach ($r as $v) {
                    $val['number'] -= $v->num;
                }
                $resule[] = [
                    'type' => 2,
                    'invoice_no' => $v->invoice_no,
                    'material_desc' => $v->material_desc,
                    'number' => $val['number'],
                ];
            }
        }
        if (count($resule) != 0) {
            return $resule;
        }
        return $push;
    }

    public function updateCommodity(Request $request)
    {
        $all = $request->all();
        $db = DB::connection('labelDB');
        if (empty($all['customs_no'])) {
            return $this->sendData(402, '报关号不能为空');
        }
        if (empty($all['inspection_no'])) {
            return $this->sendData(402, '报检号不能为空');
        }
        $item = $db->table('commodity_inspection')->where('customs_no', $all['id'])->get()->toArray();
        if (trim($item[0]->customs_no) != $all['customs_no']) {
            $customs_no = $db->table('commodity_inspection')->where('customs_no', $all['customs_no'])->get();
            if (count($customs_no) != 0) {
                return $this->sendData(402, '报关号已存在');
            }
        }
        if (trim($item[0]->inspection_no) != $all['inspection_no']) {
            $inspection_no = $db->table('commodity_inspection')->where('inspection_no', $all['inspection_no'])->get();
            if (count($inspection_no) != 0) {
                return $this->sendData(402, '报检号已存在');
            }
        }
        if($all['quarantine_no'] != '无需证书'){
            if (trim($item[0]->quarantine_no) != $all['quarantine_no']) {
                $inspection_no = $db->table('commodity_inspection')->where('quarantine_no', $all['quarantine_no'])->get();
                if (count($inspection_no) != 0) {
                    return $this->sendData(402, '检验检疫证书编号已存在');
                }
            }
        }
        $flag = [
            'customs_no' => $all['customs_no'],
            'inspection_no' => $all['inspection_no'],
            'quarantine_no' => $all['quarantine_no'],
            'certificate_time' => $all['certificate_time'],
            'updated_at' =>  date('Y-m-d H:i:s'),
        ];
        $db->table('commodity_inspection')->where('customs_no', $all['id'])->update($flag);
        return $this->sendData(200, '');
    }
}
