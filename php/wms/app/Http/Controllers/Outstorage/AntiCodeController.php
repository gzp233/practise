<?php

namespace App\Http\Controllers\Outstorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Outstorage\AntiCode;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendAntiCode;
use App\Models\Base\Product;
use Excel;
use Illuminate\Support\Facades\Log;
class AntiCodeController extends OutBaseController
{
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
     * 分页获取
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $params = [];
        if ($request->get('SHIPMENTID')) $params[] = ['SHIPMENTID', 'like', '%' . $request->get('SHIPMENTID')];
        if ($request->get('CUSTOMER')) $params[] = ['CUSTOMER', 'like', '%' . $request->get('CUSTOMER')];
        if ($request->get('PRODUCTCODE')) $params[] = ['PRODUCTCODE', 'like', '%' . $request->get('PRODUCTCODE')];
        if ($request->get('QRCODE')) $params[] = ['QRCODE', 'like', '%' . $request->get('QRCODE')];
        if ($request->get('UNIT')) $params[] = ['UNIT', 'like', '%' . $request->get('UNIT')];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['created_at', '>=', $starttime];
            $params[] = ['created_at', '<=', $endtime];
        }
        if ($request->get('starttime') && $request->get('starttime')[0] && $request->get('starttime')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('starttime')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('starttime')[1]));
            $params[] = ['starttime', '>=', $starttime];
            $params[] = ['starttime', '<=', $endtime];
        }
        if ($request->get('endtime') && $request->get('endtime')[0] && $request->get('endtime')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('endtime')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('endtime')[1]));
            $params[] = ['endtime', '>=', $starttime];
            $params[] = ['endtime', '<=', $endtime];
        }
        if ($request->get('res') && $request->get('res') == '删除成功') $params[] = ['status', '=', 3];
        if ($request->get('res') && $request->get('res') == '删除失败') $params[] = ['status', '=', 2];
        if ($request->get('res') && $request->get('res') == '未发送') $params[] = ['status', '=', 0];
        $builder = AntiCode::where($params)->orderBy($sort, 'desc')->with('dealUser');
        if ($request->get('res') && $request->get('res') == '成功') {
            $builder = $builder->where('status', 1)->whereNull('error');
        } elseif ($request->get('res') && $request->get('res') == '失败') {
            $builder = $builder->where('status', 1)->whereNotNull('error');
        }
        if ($request->get('username')) {
            $username = $request->get('username');
            $builder = $builder->whereHas('dealUser', function ($q) use ($username) {
                $q->where('username', 'like', '%' . $username);
            });
        }

        $data = $builder->paginate($limit);

        foreach ($data as $key => $item) {
            if($item->status == 2){
                $data[$key]->state = '删除失败';
            } else if($item->status == 3){
                $data[$key]->state = '删除成功';
            } else if($item->status == 1){
                $data[$key]->state = '成功';
            }
            if($item->status == 0){
                $data[$key]->state = '未发送';
            }else{
                if ($item->error) {
                    $data[$key]->state = '失败';
                } 
            }
            if ($item->status == 0) {
                $data[$key]->res = '未发送';
            } else {
                if ($item->error) {
                    $data[$key]->res = '失败';
                } else {
                    $data[$key]->res = '成功';
                }
            }
        }
        return sendData(200, '', $data);
    }

    public function sendCode(Request $request)
    {
        $id = $request->get('id');
        $codes = $request->get('codes');

        if (empty($id)) return sendData(402, 'ID错误');
        if (empty($codes)) return sendData(402, '防串货码不能为空');
        if (!$antiCode = AntiCode::findOrFail($id)) return sendData(402, '未查询到记录');
        if ($antiCode->status == 0) return sendData(402, '状态发生变化，请刷新页面重试');

        try {
            // 开启事务
            DB::beginTransaction();
            $res = $this->insertCode($codes, $antiCode);
            if ($res) throw new \Exception($res);
            $antiCode->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        SendAntiCode::dispatch();

        return sendData();
    }

    private function insertCode($codes, $antiCode)
    {
        $data = [
            'AUTHCODE' => 'JFD1fdsa867',
            'SHIPMENTID' => $antiCode->SHIPMENTID,
            'PRODUCTNAME' => $antiCode->PRODUCTNAME,
            'PRODUCTCODE' => $antiCode->PRODUCTCODE,
            'SHIPTIME' => $antiCode->SHIPTIME,
            'FROM' => $antiCode->FROM,
            'CUSTOMER' => $antiCode->CUSTOMER,
            'CUSTOMERNAME' => $antiCode->CUSTOMERNAME,
            'status' => 0,
        ];

        $product = Product::where('NewPRODUCTCD', $antiCode->PRODUCTCODE)->with('units')->first();

        $total = 0;
        foreach ($codes as $code) {
            // 验证NewProductCd 和 数量
            if (strlen($code['type_code']) == 11) {
                if ($antiCode->PRODUCTCODE != $code['type_code']) return '型号码不匹配';
                $code['NUM'] = 1;
            } else {
                if ($antiCode->PRODUCTCODE != substr($code['type_code'], 9, 11)) return '型号码不匹配';
                $code['NUM'] = (int) substr($code['type_code'], -3);
            }
            $total += $code['NUM'];
            $data['QRCODE'] = $code['code'];
            $data['box_code'] = $code['box_code'];
            $data['type_code'] = $code['type_code'];
            $data['NUM'] = $code['NUM'];
            if (empty($product->units)) return '无箱规，请先填写箱规';
            if ($code['NUM'] == 1) {
                $data['UNIT'] = "支";
            } else {
                foreach ($product->units as $unit) {
                    if ($code['NUM'] == $unit->number) {
                        $data['UNIT'] = $unit->unit_name;
                    }
                }
            }
            if (!isset($data['UNIT'])) return '未找到匹配的箱规，请检查箱规是否正确';
            if (!AntiCode::create($data)) return '防串货码插入失败';
        }
        if ($total != $antiCode->NUM) return '防串货码数量不匹配！';

        return null;
    }

    public function export(Request $request)
    {
        $parm = $request->all();
        if ($parm['id']) {
            $pieces = explode(",", $parm['id']);
            $antiCodes = AntiCode::whereIn('id', $pieces)->get();
        } else {
            $query = json_decode($parm['query']);
            $params = [];
            if (isset($query->SHIPMENTID)) $params[] = ['SHIPMENTID', 'like', '%' . $query->SHIPMENTID];
            if (isset($query->CUSTOMER)) $params[] = ['CUSTOMER', 'like', '%' . $query->CUSTOMER];
            if (isset($query->PRODUCTCODE)) $params[] = ['PRODUCTCODE', 'like', '%' . $query->PRODUCTCODE];
            if (isset($query->QRCODE)) $params[] = ['QRCODE', 'like', '%' . $query->QRCODE];
            if (isset($query->UNIT)) $params[] = ['UNIT', 'like', '%' . $query->UNIT];
            if (isset($query->created_at) && $query->created_at[0] && $query->created_at[0] != 'null') {
                $starttime = date('Y-m-d H:i:s', strtotime($query->created_at[0]));
                $endtime = date('Y-m-d H:i:s', strtotime($query->created_at[1]));
                $params[] = ['created_at', '>=', $starttime];
                $params[] = ['created_at', '<=', $endtime];
            }
            if (isset($query->res) && $query->res == '未发送') $params[] = ['status', '=', 0];

            $builder = AntiCode::where($params)->orderBy('created_at', 'desc');
            if (isset($query->res) && $query->res == '成功') {
                $builder = $builder->where('status', 1)->whereNull('error');
            } elseif (isset($query->res) && $query->res == '失败') {
                $builder = $builder->where('status', 1)->whereNotNull('error');
            }
            $antiCodes = $builder->get();
        }
        $headers = ['序号', '单号', '货品名称', '货品', '制造记号', '盒/箱/支', '数量', '所在箱'];
        $data = [];
        foreach ($antiCodes as $value) {
            $data[$value->SHIPMENTID][] = $value;
        }

        $name = date('YmdHis') . rand('1', '999');
        Excel::create($name, function ($excel) use ($headers, $data) {
            foreach ($data as $sheetName => $sheetData) {
                $excel->sheet((string) $sheetName, function ($sheet) use ($headers, $sheetData) {
                    $res = [];
                    foreach ($sheetData as $key => $value) {
                        $res[] = [
                            $key + 1,
                            $value->SHIPMENTID,
                            $value->PRODUCTNAME,
                            $value->PRODUCTCODE,
                            $value->QRCODE,
                            $value->UNIT,
                            $value->NUM,
                            $value->box_code,
                        ];
                    }
                    $sheet->setStyle([
                        'text-align' => 'center',
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center',
                        ],
                    ]);
                    $sheet->mergeCells('A1:I1');
                    $sheet->cell('A1', function ($cell) {
                        $cell->setValue('防串码明细');
                        $cell->setFont([
                            'size' => '15',
                            'bold' => true
                        ]);
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('B3', function ($cell) {
                        $cell->setValue('客户名称：');
                        $cell->setFont([
                            'bold' => true
                        ]);
                        $cell->setAlignment('left');
                    });
                    $sheet->cell('C3', function ($cell) use ($sheetData) {
                        $cell->setValue($sheetData[0]->CUSTOMERNAME);
                        $cell->setAlignment('left');
                    });
                    $sheet->cell('B4', function ($cell) {
                        $cell->setValue('客户代码：');
                        $cell->setFont([
                            'bold' => true
                        ]);
                        $cell->setAlignment('left');
                    });
                    $sheet->cell('C4', function ($cell) use ($sheetData) {
                        $cell->setValue($sheetData[0]->CUSTOMER);
                        $cell->setAlignment('left');
                    });
                    $sheet->row(6, $headers);
                    $sheet->cells('A6:I6', function ($cells) {
                        $cells->setFont([
                            'bold' => true
                        ]);
                    });
                    $sheet->rows($res);
                    $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
                    foreach ($columns as $column) {
                        for ($i = 6; $i <= (count($res) + 6); $i++) {
                            $sheet->cell($column . $i, function ($cell) {
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                    }

                    $sheet->cell('B' . (count($res) + 10), function ($cell) {
                        $cell->setValue('客户签字：');
                        $cell->setFont([
                            'bold' => true
                        ]);
                        $cell->setAlignment('left');
                    });
                    $sheet->cell('C' . (count($res) + 10), function ($cell) {
                        $cell->setBorder('none', 'none', 'thick', 'none');
                    });
                    $sheet->setWidth(array(
                        'A'     =>  '8',
                        'B'     =>  '15',
                        'C'     =>  '45',
                        'D'     =>  '15',
                        'E'     =>  '20',
                        'F'     =>  '10',
                        'G'     =>  '8',
                        'H'     =>  '8',
                        'I'     =>  '8',
                    ));
                });
            }
        })->export('xls');
    }
    public function del(Request $request)
    {
        $all = $request->get('goods');
        $ids = $request->get('id');
        $id = array_unique($all);
        if (count($id) > 1) {
            return sendData(402, '只能删一单');
        }
        try {
            // 开启事务 
            DB::beginTransaction();
            $odd = AntiCode::whereIn('id', $ids)->get();
            $res = [];
            foreach ($odd as $k =>$key) {
                $adj = DB::table('adj_out_dirt')->where('AdjustNo', $key->SHIPMENTID)->where('ProductCd',$key->PRODUCTCODE)->get();
                $move = DB::table('move_out_dirt')->where('MoveNo', $key->SHIPMENTID)->where('ProductCD',$key->PRODUCTCODE)->get();
                $ord = DB::table('ord_out_dirt')->where('OrderNo', $key->SHIPMENTID)->where('ProductCd',$key->PRODUCTCODE)->get();
                if (count($adj) != 0) {
                    foreach ($adj as $a) {
                        $tag = DB::table('adj_out_dirt_tag')->where('related_id', $a->id)->first();
                        if ($tag->status != '发货完成') {
                            return sendData(402, '该数据没出库');
                        }
                    }
                } else if (count($move) != 0) {
                    foreach ($move as $m) {
                        $tag = DB::table('move_out_dirt_tag')->where('related_id', $m->id)->first();
                        if ($tag->status != '发货完成') {
                            return sendData(402, '该数据没出库');
                        }
                    }
                } else if (count($ord) != 0) {
                    foreach ($ord as $o) {
                        $tag = DB::table('ord_out_dirt_tag')->where('related_id', $o->id)->first();
                        if ($tag->status != '发货完成') {
                            return sendData(402, '该数据没出库');
                        }
                    }
                } else {
                    return sendData(402, '出库单号不存在');
                }
                $name = date('Y-m-d');
                Log::useFiles(storage_path('logs/AntiCode/error' . $name . '.log'));
                Log::info('key' . $key);
                Log::info('error' . $key->error);
                Log::info('status' . $key->status);

                if (!$key->error) {
                    if ($key->status == 1) {
                        $resule = $this->sendDelCode($key);
                        Log::info('resule' . $resule);
                        if ($resule) {
                            if ($resule->breturn == false) {
                                $res[$k]['id'] = $k;
                                $res[$k]['QRCODE'] = $key->QRCODE;
                                $res[$k]['errorinfo'] = $resule->errorinfo;
                                AntiCode::where('id', $key->id)->update(['status' => '2','updated_at' => date('Y-m-d H:i:s'),'del_name' => $this->user->username]);
                                $name = date('Y-m-d');
                                Log::useFiles(storage_path('logs/AntiCode/error' . $name . '.log'));
                                //记录日志
                                Log::info('操作人：' . $this->user->username . '单号：' . $key->SHIPMENTID.'防串货号：' . $key->QRCODE. '失败原因:' . $resule->errorinfo);
                                continue;
                            }
                        } else {
                            continue;
                        }
                    } else if ($key->status == 2) {
                        $resule = $this->sendDelCode($key);
                        Log::info('resule2' . $resule);
                        if ($resule) {
                            if ($resule->breturn == false) {
                                $res[$k]['id'] = $k;
                                $res[$k]['QRCODE'] = $key->QRCODE;
                                $res[$k]['errorinfo'] = $resule->errorinfo;
                                $name = date('Y-m-d');
                                Log::useFiles(storage_path('logs/AntiCode/error' . $name . '.log'));
                                Log::info('操作人：' . $this->user->username . '单号：' . $key->SHIPMENTID.'防串货号：' . $key->QRCODE. '失败原因:' . $resule->errorinfo);
                                continue;
                            }
                        } else {
                            continue;
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
                $product = DB::table('product')->where('PRODUCTCD',$key->PRODUCTCODE)->first();

                $record = DB::table('goods_record')->where('odd', $id)->where('product_id',$product->id)->get();

                foreach ($record as $val) {
                    $data = [
                        'product_id' => $val->product_id,
                        'state_name' => $val->state_name,
                        'origin_stock_no' => $val->origin_stock_no,
                        'CHARG' => $val->CHARG,
                        'number' => $val->number,
                        'odd' => $val->odd,
                        'QRCODE' => $key->QRCODE,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    DB::table('anti_code_del')->insert($data);
                }
                AntiCode::where('id', $key->id)->update(['status' => '3','updated_at' => date('Y-m-d H:i:s'),'del_name' => $this->user->username]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        if(count($res) == 0){
            return sendData(200, '成功');
        }else{
            $error = '';
            foreach($res as $v){
                $error .= '防串货码：'.$v['QRCODE'].'失败原因：'.$v['errorinfo'];
                // "防串货码：'.'失败原因：'$v->errorinfo"
            }
            return sendData(402, $error);

        }
    }

    private function sendDelCode($anticode)
    {
        $name = date('Y-m-d');
        Log::useFiles(storage_path('logs/AntiCode/error' . $name . '.log'));
        Log::info('anticode' . $anticode);
        Log::info('AUTHCODE' . $anticode->AUTHCODE);

        $url = env('DELCODE_URL');
        Log::info('url' . $url);
        Log::info('QRCODE' . $anticode->QRCODE);
        Log::info('username' . $this->user->username);

        if (!$url) return;
        $data = [
            'AUTHCODE' => $anticode->AUTHCODE,
            'QRCODE' => $anticode->QRCODE,
            'EMPLOYEE' => $this->user->username,
        ];
        Log::info('data' . $data);

        return;
        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: application/json',
        //     'Content-Length: ' . strlen(json_encode($data)),
        // ));
        // $res = json_decode(curl_exec($curl));
        // curl_close($curl);
        // Log::info('res' . $res);
        // Log::info('curl' . $curl);
        // return $res;
    }
}
