<?php

namespace Modules\Label\Http\Controllers\Barcode;

use Illuminate\Http\Request;
use Modules\Label\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Modules\Label\Models\Invoice;
use Modules\Label\Models\ItemStock;
use Modules\Label\Models\QueryResult;
use Excel;
use PHPExcel_IOFactory;
use baidu\ocr\AipOcr;
use Modules\Label\Models\Item;

class QueryBarcodeController extends BaseController
{
    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function getById(Request $request)
    {
        $db = DB::connection('labelDB');
        $all = $request->get('invoice_no');
        $str = strripos($all, '-');
        $invoice_no = substr($all, 0, $str);
        $res = $db->table('invoices')->where('invoice_no', $invoice_no)->where('status', '<>', '0')->get();
        if (count($res) == 0) {
            return $this->sendData(402, '该单号未导入或未入库', '该单号未导入或未入库');
        }
        return $this->sendData(200, '', '成功');
    }

    public function getBySee(Request $request)
    {
        $invoice_no = $request->get('invoice_no');
        if (!$invoice_no) return sendData(402, '发票号不能为空');
        $data = json_decode(Redis::get('chaxun::barcode::' . $invoice_no));
    }

    // 从redis中获取数据
    private function getFromRedis($id)
    {
        return json_decode(Redis::get('ocr::' . $id));
    }

    // 从redis中获取数据
    private function getArrayFromRedis($id)
    {
        return json_decode(Redis::get('ocr::' . $id), true);
    }

    // 保存数据到redis
    private function setToRedis($id, $data)
    {
        if ($data) {
            Redis::set('ocr::' . $id, json_encode($data));
        } else {
            Redis::del('ocr::' . $id);
        }

        return true;
    }

    public function upload(Request $request)
    {
        $base64_img = base64_decode(trim($request->get('id')));
        $client = new AipOcr(config('ocr.APP_ID'), config('ocr.API_KEY'), config('ocr.SECRET_KEY'));
        $result = $client->basicGeneral($base64_img);
        foreach ($result as $key => $val) {
            if ($key == 'words_result') {
                foreach ($val as $list) {
                    if (strlen($list['words']) > 10) {
                        continue;
                    }
                    if (preg_match("/^[a-zA-Z0-9]{4,10}$/", $list['words'])) {
                        return $this->sendData(200, '', $list['words']);
                    } else {
                        continue;
                    }
                }
            } else {
                continue;
            }
        }
        return sendData(200, '', '');
    }

    public function ocrList(Request $request)
    {
        $all = $request->all();
        if (count($all) == 0) {
            return $this->sendData(402, '制造记号不能为空', '制造记号不能为空');
        }
        $userId = $this->user->id;
        $id = $all['invoice_no'] . '-' . $all['support_no'] . '-' . $userId;
        $list = $this->getFromRedis($id);
        if ($list) {
            $res = [
                'support_no' => $all['support_no'],
                'items' => $list
            ];
        } else {
            return $this->sendData(200, '失败', ['support_no' => $all['support_no']]);
        }
        return $this->sendData(200, '成功', $res);
    }

    public function ocrWrite(Request $request)
    {
        $all = $request->all();
        if (count($all) == 0) {
            return $this->sendData(402, '制造记号不能为空', '制造记号不能为空');
        }
        $userId = $this->user->id;
        $id = $all['invoice_no'] . '-' . $all['support_no'] . '-' . $userId;
        $data = $all['items'];
        $this->setToRedis($id, $data);
        $list = $this->getFromRedis($id);
        $record = $list;
        $fit = 0;
        $ids = $all['invoice_no'] . '-' . $all['support_no'] . '-' . $userId . '-' . '1';
        if ($list) {
            $last_names = array_column($list, 'num');
            array_multisort($last_names, SORT_ASC, $list);
            foreach ($list as $val) {
                //截取大于六位的制造记号的前六位
                if (strlen($val->mark) > 6) {
                    $val->mark = substr($val->mark, 0, 6);
                }
                //正则判断是不是正常的制造记号
                if (preg_match("/^[0-9]{4}[a-zA-Z]*$/", $val->mark)) {
                    continue;
                } else {
                    $res = [
                        'support_no' => $all['support_no'],
                        'items' => end($record)
                    ];
                    $this->setToRedis($ids, $res);
                    return $this->sendData(200, '异常', $res);
                }
            }
            //判断有几个制造记号
            if (count($list) == 3) {
                for ($i = 0; $i < 2; $i++) {
                    $len = substr($list[1]->mark, 0, $list[0]->num);
                    if ($len == $list[0]->mark) {
                        $len1 = substr($list[2]->mark, 0, $list[1]->num);
                        if ($len1 == $list[1]->mark) {
                            continue;
                        } else {
                            $fit = 1;
                        }
                    } else {
                        $fit = 1;
                    }
                }
            } elseif (count($list) == 2) {
                if ($list[0]->mark == $list[1]->mark) {
                    $fit = 0;
                } else {
                    $fit = 1;
                }
            }
            if ($fit == 1) {
                $res = [
                    'support_no' => $all['support_no'],
                    'items' => end($record)
                ];
            } else {
                $res = [
                    'support_no' => $all['support_no'],
                    'items' => end($list)
                ];
            }
        } else {
            $res = [
                'support_no' => $all['support_no'],
                'items' => ''
            ];
        }
        $this->setToRedis($ids, $res);
        return $this->sendData(200, '成功', $res);
    }

    public function getByNo(Request $request)
    {

        $all = $request->all();
        $db = DB::connection('labelDB');
        $result = $db->table('invoices')->where('invoice_no', $all['invoice_no'])->where('material_code', $all['newCode'])->get();
        if (count($result) == 0) {
            return $this->sendData(402, '该发票号下无该商品');
        }
        $all['mark'] = $all['mark'] === null ? '' : $all['mark'];
        $id =  $all['invoice_no'] . '-' . $all['support_no'];
        $down_id =  $all['invoice_no'] . '-' . $all['support_no'] . '-' . 'down';
        $data = $this->getFromRedis($id);
        $down = $this->getArrayFromRedis($down_id);
        // Redis::del('ocr::' . $down_id);
        // Redis::del('ocr::' . $id);
        // dd();
        $userId = $this->user->id;
        $mark_id = $all['invoice_no'] . '-' . $all['support_no'] . '-' . $userId;
        $mark_id = $this->getFromRedis($mark_id);
        $new_array = array();
        if ($mark_id) {
            foreach ($mark_id as $k => $v) {
                if ($v->type == '箱码') {
                    $new_array['box_mark'] = $v;
                }
                if ($v->type == '盒码') {
                    $new_array['case_mark'] = $v;
                }
                if ($v->type == '支码') {
                    $new_array['branch_mark'] = $v;
                }
            }
        }
        if (!isset($new_array['box_mark'])) {
            $box_mark = '';
        } else {
            $box_mark = $new_array['box_mark']->mark;
        }
        if (!isset($new_array['case_mark'])) {
            $case_mark = '';
        } else {
            $case_mark = $new_array['case_mark']->mark;
        }
        if (!isset($new_array['branch_mark'])) {
            $branch_mark = '';
        } else {
            $branch_mark = $new_array['branch_mark']->mark;
        }
        if($all['type'] == 1){
            $case_mark = '';
            $box_mark = '';
            $branch_mark = '';
        }
        if ($data) {
            $allkey = $all['mark'] . '-' . $all['invoice_no'] . '-' . $all['support_no'] . '-' . $all['newCode'];
            if (array_key_exists($allkey, (array) $data)) {
                $data->$allkey->num += $all['num'];
            } else {
                $arr[$allkey] = [
                    'invoice_no' => $all['invoice_no'],
                    'support_no' => $all['support_no'],
                    'mark' => $all['mark'],
                    'newCode' => $all['newCode'],
                    'num' => (int) $all['num'],
                    'box_mark' => $box_mark,
                    'case_mark' => $case_mark,
                    'branch_mark' => $branch_mark,
                ];
                $data = array_merge((array) $data, $arr);
            }
            $this->setToRedis($id, $data);
        } else {
            $key = $all['mark'] . '-' . $all['invoice_no'] . '-' . $all['support_no'] . '-' . $all['newCode'];
            $data[$key] = [
                'invoice_no' => $all['invoice_no'],
                'support_no' => $all['support_no'],
                'mark' => $all['mark'],
                'newCode' => $all['newCode'],
                'num' => (int) $all['num'],
                'box_mark' => $box_mark,
                'case_mark' => $case_mark,
                'branch_mark' => $branch_mark,
            ];
            $this->setToRedis($id, $data);
        }
        if ($down) {
            end($down);
            $marks = [
                'box_mark' => $box_mark,
                'case_mark' => $case_mark,
                'branch_mark' => $branch_mark,
                'index' => key($down) + 1
            ];
            $all = array_merge($all, $marks);
            array_push($down, $all);
            $this->setToRedis($down_id, $down);
        } else {
            $marks = [
                'box_mark' => $box_mark,
                'case_mark' => $case_mark,
                'branch_mark' => $branch_mark,
            ];
            $all = array_merge($all, $marks);
            $all['index'] = 0;
            $this->setToRedis($down_id, [$all]);
        }
        $res = $this->getFromRedis($id);
        $datas = $this->getFromRedis($down_id);
        $result = [
            'code' => 200,
            'message' => '成功',
            'data' => $res,
            'count' => count((array) $res),
            'datas' => $datas
        ];
        return response()->json($result);
    }

    public function del(Request $request)
    {
        $all = $request->all();
        $index = $request->get('index');
        $id =  $all['invoice_no'] . '-' . $all['support_no'];
        $down_id =  $all['invoice_no'] . '-' . $all['support_no'] . '-' . 'down';
        $redis = $this->getFromRedis($id);
        $down = $this->getArrayFromRedis($down_id);
        $idds = array_column($down, 'index');
        if (!in_array($index, $idds)) {
            return $this->sendData(402, '正在删除，请稍后…');
        }
        unset($down[$index]);
        $this->setToRedis($down_id, $down);
        $key = $all['mark'] . '-' . $all['invoice_no'] . '-' . $all['support_no'] . '-' . $all['newCode'];
        $keyId =  $all['mark'] . '-' . $all['invoice_no'] . '-' . $all['support_no'] . '-' . $all['newCode'] . '-' . 'del';
        if (Redis::get($keyId) && Redis::get($keyId) == 1) {
            return $this->sendData(402, '正在删除，请稍后…');
        }
        Redis::set($keyId, 1);
        
        $down = $this->getArrayFromRedis($down_id);
        unset($down[$index]);
        $this->setToRedis($down_id, $down);
        if (array_key_exists($key, (array) $redis)) {
            $redis->$key->num -= $all['num'];
            if ($redis->$key->num <= 0) {
                unset($redis->$key);
            }
        } else {
            $res = $this->getFromRedis($id);
            $result = [
                'code' => 200,
                'message' => '成功',
                'data' => $res,
                'count' => count((array) $res),
                'datas' => $down
            ];
            Redis::del($keyId);
            return response()->json($result);
        }

        $this->setToRedis($id, $redis);
        $res = $this->getFromRedis($id);
        Redis::del($keyId);
        $result = [
            'code' => 200,
            'message' => '成功',
            'data' => $res,
            'count' => count((array) $res),
            'datas' => $down
        ];
        return response()->json($result);
    }
    //判断托号是否存在
    public function support(Request $request)
    {
        $all = $request->all();
        $db = DB::connection('labelDB');
        $id =  $all['invoice_no'] . '-' . $all['support_no'];
        $data = $this->getFromRedis($id);
        $res = [];
        if (!$data) {
            $result = $db->table('item_stock')->where('support_no', $all['support_no'])->where('invoice_no', $all['invoice_no'])->get()->toArray();
            if (count($result) != 0) {
                foreach ($result as $key) {
                    if (!empty($key->branch_mark)) {
                        $mark = $key->branch_mark;
                    } else if (!empty($key->case_mark)) {
                        $mark = $key->case_mark;
                    } else {
                        $mark = $key->box_mark;
                    }
                    $k = $mark . '-' . $key->invoice_no . '-' . $key->support_no . '-' . $key->material_code;
                    $res[$k] = [
                        'invoice_no' => $key->invoice_no,
                        'support_no' => $key->support_no,
                        'mark' => $mark,
                        'newCode' => $key->material_code,
                        'num' => (int) $key->num,
                        'box_mark' => $key->box_mark,
                        'case_mark' => $key->case_mark,
                        'branch_mark' => $key->branch_mark,
                    ];
                }
                $this->setToRedis($id, $res);
            } else {
                $this->setToRedis($id, null);
            }
        }
        $data = $this->getFromRedis($id);
        return $this->sendData(200, '成功', $data);
    }
    public function supports(Request $request)
    {
        $all = $request->all();
        $db = DB::connection('labelDB');
        $result = $db->table('item_stock')->where('support_no', $all['support_no'])->first();
        if ($result) {
            if ($result->invoice_no == $all['invoice_no']) {
                $down_id =  $all['invoice_no'] . '-' . $all['support_no'] . '-' . 'down';
                $data = $this->getFromRedis($down_id);
                return $this->sendData(200, '成功', $data);
            } else {
                return $this->sendData(200, '失败');
            }
        } else {
            $down_id =  $all['invoice_no'] . '-' . $all['support_no'] . '-' . 'down';
            $data = $this->getFromRedis($down_id);
            return $this->sendData(200, '成功', $data);
        }
    }
    public function doSubmit(Request $request)
    {
        $all = $request->get('items');
        $len = $request->get('count');
        $userId = $this->user->id;
        $genId = $this->genId();
        if (!$all) {
            return $this->sendData(402, '没扫入任何商品');
        }
        foreach ($all as $list) {
            $uid = $list['invoice_no'] . '-' . $list['support_no'] . '-' . $userId;
            $id =  $list['invoice_no'] . '-' . $list['support_no'];
            $down_id =  $list['invoice_no'] . '-' . $list['support_no'] . '-' . 'down';
            $ids = $list['invoice_no'] . '-' . $list['support_no'] . '-' . $userId . '-' . '1';
        }
        $count = $this->getFromRedis($down_id);
        $count = count((array) $count);
        if ($count != $len) {
            return $this->sendData(402, '数据不符，请刷新页面');
        }
        try {
            // 开启事务
            DB::beginTransaction();
            foreach ($all as $key) {
                $result = Invoice::where('invoice_no', $key['invoice_no'])->where('material_code', $key['newCode'])->first();
                if (!$result) {
                    return $this->sendData(402, '该产品不存在该发票号');
                }
                $arr = [
                    'num' => $key['num'],
                    'invoice_no' => $key['invoice_no'],
                    'item_no' => $result->item_no,
                    'material_code' => $key['newCode'],
                    'item_name' => $result->material_desc,
                    'location_no' => '暂存区',
                    'support_no' => $key['support_no'],
                    'box_mark' => $key['box_mark'] === null ? '' : $key['box_mark'],
                    'case_mark' => $key['case_mark'] === null ? '' : $key['case_mark'],
                    'branch_mark' => $key['branch_mark'] === null ? '' : $key['branch_mark'],
                    'valid_month' => '0',
                    'status' => '2',
                    'production_date' => '',
                    'expired_at' => '',
                ];
                $data = [
                    'task_no' => $genId,
                    'item_no' => $result->item_no,
                    'location_no' => '暂存区',
                    'support_no' => $key['support_no'],
                    'material_code' => $key['newCode'],
                    'item_name' => $result->material_desc,
                    'invoice_no' => $key['invoice_no'],
                    'box_mark' => $key['box_mark'] === null ? '' : $key['box_mark'],
                    'case_mark' => $key['case_mark'] === null ? '' : $key['case_mark'],
                    'branch_mark' => $key['branch_mark'] === null ? '' : $key['branch_mark'],
                    'user_id' => $userId,
                ];
                $result->confirm_num += $key['num'];
                $result->stage_num -= $key['num'];
                $result->save();
                $stage = [
                    'invoice_no' =>$key['invoice_no'],
                    'material_code' =>$key['newCode'],
                    'status' =>1,
                ];
                $item = ItemStock::where($stage)->first();
                if(!$item){
                    $list = [
                        'num' => 0 - $key['num'],
                        'invoice_no' => $key['invoice_no'],
                        'item_no' => $result->item_no,
                        'material_code' => $key['newCode'],
                        'item_name' => $result->material_desc,
                        'location_no' => '暂存区',
                        'support_no' => '',
                        'box_mark' => '',
                        'case_mark' => '',
                        'branch_mark' => '',
                        'valid_month' => '0',
                        'status' => '1',
                        'production_date' => '',
                        'expired_at' => '',
                    ];
                    $this->itemIn($list);
                }else{
                    $item->num -= $key['num'];
                    $item->save();
                    if($item->num == 0){
                        $item->delete();
                    }
                }
                $this->itemIn($arr);
                $res = QueryResult::where($data)->first();
                if ($res) {
                    $res->num += $key['num'];
                    $res->save();
                } else {
                    $data['num'] = (int) $key['num'];
                    QueryResult::create($data);
                }
            }

            $count = $this->getFromRedis($id);
            $count = count((array) $count);
            $this->setToRedis($id, null);
            $this->setToRedis($uid, null);
            $this->setToRedis($ids, null);
            $this->setToRedis($down_id, null);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $this->sendData(200, '查询完成', $count);
    }
    public function ids(Request $request)
    {
        $all = $request->all();
        $userId = $this->user->id;
        $id = $all['invoice_no'] . '-' . $all['support_no'] . '-' . $userId . '-' . '1';
        $res = $this->getFromRedis($id);
        return $this->sendData(200, '成功', $res);
    }
}
