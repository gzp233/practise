<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/11/20
 * Time: 15:48
 */
namespace App\Http\Controllers\Barcode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\Redis;
use App\Models\Base\Product;
use function GuzzleHttp\json_encode;
use Illuminate\Support\Facades\Log;

class BarcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    private function getProductByNo($id)
    {
        if (Redis::exists($id)) {
            $product = json_decode(Redis::get($id));
        } else {
            $data = Product::where('PRODUCTCD', $id)->with('units')->first();
            Redis::set($id, json_encode($data));
            $product = json_decode(Redis::get($id));
        }

        return $product;
    }

    private function getProductByBarcode($id)
    {
        if (Redis::exists('barcode::' . $id)) {
            $product = json_decode(Redis::get('barcode::' . $id));
            if ($product == 'null' || !$product) {
                $data = Product::where('barCode', $id)->with('units')->first();
                if (!$data) {
                    return false;
                }
                Redis::set('barcode::' . $id, json_encode($data));
                $product = json_decode(Redis::get('barcode::' . $id));
            }
        } else {
            $data = Product::where('barCode', $id)->with('units')->first();
            if (!$data) {
                return false;
            }
            Redis::set('barcode::' . $id, json_encode($data));
            $product = json_decode(Redis::get('barcode::' . $id));
        }

        return $product;
    }

    public function getBarCode(Request $request)
    {
        $id = $request->get('id');
        $type_code = $request->get('type_code');
        $product = $this->getProductByBarcode($type_code);
        if (!$product) {
            return sendData(402, '该支码不存在');
        }
        // 检查是否存在，是否满了
        $data = $this->getFromRedis($id);
        if (!$data) return sendData(402, '订单错误');
        $flag = 0;
        foreach ($data->goods as $item) {
            if ($item->PRODUCTCD == $product->PRODUCTCD && $item->scanNumber < $item->number) $flag = 1;
        }

        if (!$flag) return sendData(402, '商品不在此单或已扫完');

        return sendData(200, '', $product);
    }

    public function getErrors(Request $request)
    {
        $id = $request->get('id');
        $errors = json_decode(Redis::get('fuhe::barcode::erros::' . $id));

        return sendData(200, '', $errors);
    }

    public function stockOut(Request $request)
    {

        $id = $request->get('id');
        $data = $this->getFromRedis($id);
        if (!$data) return sendData(402, '状态发生变化，刷新页面重试！');
        $errors = [];

        foreach ($data->goods as $item) {
            if ($item->number > $item->scanNumber) {
                $errors[] = [
                    'PRODUCTCD' => $item->PRODUCTCD,
                    'available_time' => $item->available_time,
                    'number' => $item->number - $item->scanNumber
                ];
            }
        }
        if ($errors) {
            Redis::setex('fuhe::barcode::erros::' . $id, 3600, json_encode($errors));
            return sendData(200, '', $errors);
        }

        try {
            // 开启事务
            DB::beginTransaction();
            $insert = [];
            foreach ($data->codes as $value) {
                $product = Product::where('PRODUCTCD',$value->type_code)->first();
                $insert[] = [
                    'vbeln' => $value->id,
                    'number' => $value->number,
                    'case' => $value->box_code,
                    'available_time' => $value->date_code,
                    'product_id' => $product->id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $moveCode = DB::table('move_out_dirt')->where('MoveNo', $value->id)->pluck('id');
                $adjCode = DB::table('adj_out_dirt')->where('AdjustNo', $value->id)->pluck('id');
                $ordCode = DB::table('ord_out_dirt')->where('OrderNo', $value->id)->pluck('id');
                DB::table('move_out_dirt_tag')->whereIn('related_id', $moveCode)->update(['status' => '待发运', 'fh_end' => date('Y-m-d H:i:s')]);
                DB::table('adj_out_dirt_tag')->whereIn('related_id', $adjCode)->update(['status' => '待发运', 'fh_end' => date('Y-m-d H:i:s')]);
                DB::table('ord_out_dirt_tag')->whereIn('related_id', $ordCode)->update(['status' => '待发运', 'fh_end' => date('Y-m-d H:i:s')]);
            }
            DB::table('binning')->insert($insert);
            $this->setToRedis($id, null);
            Redis::del('fuhe::shunxu::' . $id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData();
    }

    // 根据单号获取扫码信息
    public function getFuheOrderByNo(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return sendData(402, 'id不能为空');
        // Redis::del('fuhe::barcode::' . $id);
        $data = $this->getFromRedis($id);
        $codes = $this->getArray($data->codes);
        $boxes = $this->getArray($data->boxes);
        $otherBoxes = $this->getArray($data->otherBoxes);
        $last_names = array_column($codes,'box_code');
        array_multisort($last_names,SORT_DESC,$codes);
        return sendData(200, '', ['codes' => $codes, 'boxes' => $boxes, 'otherBoxes' => $otherBoxes]);
    }


    private function getArray($items)
    {
        $data = [];
        foreach ($items as $item) {
            $data[] = $item;
        }
        return $data;
    }

    public function delCode(Request $request)
    {
        $row = $request->all();
        $sum = 0;
        $list = 0;
        // Redis::set('fuhe::shunxu::'.$row['id'],'');
        // Redis::set('fuhe::barcode::'.$row['id'],'');
        // dd(1);
        $data = $this->getFromRedis($row['id']);
        $codes = $this->getArray($data->codes);
        $boxes = $this->getArray($data->boxes);

        $product = $this->getProductByNo($row['type_code']);
        $index = $product->PRODUCTCD . '_' . $row['date_code'];
        $lock = $row['id'].$row['box_code'] . '-' . $row['type_code'] . '-' . $row['date_code'].'lock';
        if (Redis::get($lock) && Redis::get($lock) === 1) {
            return sendData(202, '回退失败', $sum);
        }
        Redis::set($lock, 1);
        $delcode = $row['box_code'] . '-' . $row['type_code'] . '-' . $row['date_code'];
        $red = json_decode(Redis::get('fuhe::shunxu::' . $row['id']));

        foreach ($red as $key => $v) {
            if ($key == $delcode) {
                $num = array_pop($v);
                $red->$key = $v;
            } else {
                continue;
            }
        };
        foreach ($codes as $key => $value) {
            if($value->box_code != $row['box_code']){
                continue;
            }
            if (
                $value->box_code == $row['box_code'] && $value->number == $row['number']
                && $value->type_code == $row['type_code'] && $value->date_code == $row['date_code']
            ) {
                if(!$num){
                    $num = $value->number;
                 }
                $codes[$key]->number -= $num;
                $sum = $codes[$key]->number;
                if ($codes[$key]->number == 0) {
                    unset($codes[$key]);
                }
                if ($row['unit'] == '箱') {
                    $boxes = array_diff($boxes, [$row['box_code']]);
                } else {
                    $otherBoxes = [];
                    foreach ($codes as $value) {
                        if (!in_array($value->box_code, $otherBoxes)) $otherBoxes[] = $value->box_code;
                    }
                    $data->otherBoxes = $otherBoxes;
                }
                $data->codes = $codes;
                $data->boxes = $boxes;
                $data->goods->$index->scanNumber -= $num;
                $this->setToRedis($row['id'], $data);
                Redis::set('fuhe::shunxu::' . $row['id'], json_encode($red));
                Redis::del($lock);
                break;
            }else {
                continue;
            }
        }
        return sendData(200, '', $sum);
    }

    public function saveCode(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');
        $str = $request->get('str');
        $box_code = $request->get('box_code');
        if (!$id || !$type || !$box_code || !$str) return sendData(402, '参数缺失');
        $data = $this->getFromRedis($id);
        
        $codes = [];
        foreach ($data->codes as $item) {
            $codes[$item->box_code . '-' . $item->type_code . '-' . $item->date_code] = $item;
        }
        $product = '';
        $pro = 0;
        if ($type == 1) {
            if (strpos($str, '%') !== false || (int)substr($str, 4, 3) > 366) return sendData(402, '型号码错误');
            $type_code = substr($str, 9, 11);
            $products = Product::where('NewPRODUCTCD',$type_code)->first();
            $number = (int)substr($str, -3);
            $product = $this->getProductByNo($products->PRODUCTCD);
            if(!$product) return sendData(402, '该商品不存在');
            if (!$product->units) return sendData(402, '商品无箱规');
            $flag = 0;
            $unit = '';
            foreach ($product->units as $item) {
                if ($number == $item->number) {
                    $flag = 1;
                    $unit = $item->unit_name;
                }
            }
            if (!$flag || !in_array($unit, ['箱', '盒'])) return sendData(402, '商品箱规错误');
            $date_code = $this->parseXiaoqi($product, substr($str, 3, 4));
            if($unit == '箱'){
                foreach($codes as $key){
                   if($key->box_code == $box_code){
                    return sendData(402, '同一个箱码只能扫一次');
                   }
                }
            }
            if (!$date_code) return sendData(402, '商品' . $type_code . '解析错误，请检查有效期是否存在');
            $pro = 2;
            foreach ($codes as $key => $val) {
                if ($key == $box_code . '-' . $product->PRODUCTCD . '-' . $date_code) {
                    $index = $product->PRODUCTCD . '_' . $date_code;
                    if ($data->goods->$index->scanNumber + $number > $data->goods->$index->number) return sendData(402, '超出数量限制');
                    $data->goods->$index->scanNumber += $number;
                    $val->number += $number;
                    $arr = [
                        $box_code . '-' . $products->PRODUCTCD . '-' . $date_code => [$number],
                    ];
                    $red = json_decode(Redis::get('fuhe::shunxu::' . $id));
                    $k = $box_code . '-' . $products->PRODUCTCD . '-' . $date_code;
                    if ($red) {
                        if (array_key_exists($k, $red)) {
                            array_push($red->$k, $number);
                            Redis::set('fuhe::shunxu::' . $id, json_encode($red));
                        }
                    } else {
                        Redis::set('fuhe::shunxu::' . $id, json_encode($arr));
                    }
                    $pro = 1;
                    $this->setToRedis($id, $data);
                    return sendData(200, '', $val);
                }
            }
        } else {
            list($nPcd, $date_code) = explode('_', $str);
            $product = $this->getProductByNo($nPcd);
            foreach ($codes as $key => $val) {
                if ($key == $box_code . '-' . $product->PRODUCTCD . '-' . $date_code) {
                    $index = $product->PRODUCTCD . '_' . $date_code;
                    if ($data->goods->$index->scanNumber + 1 > $data->goods->$index->number) return sendData(402, '超出数量限制');
                    $data->goods->$index->scanNumber += 1;
                    $val->number += 1;
                    $number = 1;
                    $arr = [
                        $box_code . '-' . $product->PRODUCTCD . '-' . $date_code => [$number],
                    ];
                    $red = json_decode(Redis::get('fuhe::shunxu::' . $id));
                    $k = $box_code . '-' . $product->PRODUCTCD . '-' . $date_code;
                    if ($red) {
                        if (array_key_exists($k, $red)) {
                            array_push($red->$k, $number);
                            Redis::set('fuhe::shunxu::' . $id, json_encode($red));
                        }
                    } else {
                        Redis::set('fuhe::shunxu::' . $id, json_encode($arr));
                    }
                    $pro = 1;
                    $this->setToRedis($id, $data);
                    return sendData(200, '', $val);
                }
            }
        }
        if ($pro == 1) {
            $pro = 0;
            $this->setToRedis($id, $data);
            return sendData(200, '', $val);
        } else if ($pro == 2) {
            $pro = 0;
            $code = [
                'id' => $id,
                'type_code' => $products->PRODUCTCD,
                'unit' => $unit,
                'number' => $number,
                'date_code' => $date_code,
                'box_code' => $box_code,
            ];
            $codes[] = $code;
            $data->codes = $codes;
            $index = $product->PRODUCTCD . '_' . $code['date_code'];
          
            if (!isset($data->goods->$index)) return sendData(402, '有效期为' . $code['date_code'] . '的商品' . $code['type_code'] . '不在此单');
            if ($data->goods->$index->scanNumber + $code['number'] > $data->goods->$index->number) return sendData(402, '超出数量限制');
            $data->goods->$index->scanNumber += $code['number'];
            $boxes = $this->getArray($data->boxes);
            // dd($boxes);
            $otherBoxes = $this->getArray($data->otherBoxes);
            if ($code['unit'] == '箱') {
                if (in_array($code['box_code'], $boxes) || in_array($code['box_code'], $otherBoxes)) return sendData(402, '箱码不能重复');
                $boxes[] = $code['box_code'];
            } else {
                if (!in_array($code['box_code'], $otherBoxes)) $otherBoxes[] = $code['box_code'];
            }
            $data->boxes = $boxes;
            $data->otherBoxes = $otherBoxes;
            $arr = [
                $box_code . '-' . $products->PRODUCTCD . '-' . $date_code => [$number],
            ];
            $red = json_decode(Redis::get('fuhe::shunxu::' . $id));
            $k = $box_code . '-' . $products->PRODUCTCD . '-' . $date_code;
            if ($red) {
                if (array_key_exists($k, $red)) {
                    array_push($red->$k, $number);
                } else {
                    $red = json_decode(json_encode($red), true);
                    $red = array_merge($red, $arr);
                    Redis::set('fuhe::shunxu::' . $id, json_encode($red));
                }
            } else {
                Redis::set('fuhe::shunxu::' . $id, json_encode($arr));
            }
            $this->setToRedis($id, $data);
        } else {
            $code = [
                'id' => $id,
                'type_code' => $product->PRODUCTCD,
                'unit' => '支',
                'number' => 1,
                'date_code' => $date_code,
                'box_code' => $box_code,
            ];
            $codes[] = $code;
            $data->codes = $codes;

            $index = $product->PRODUCTCD . '_' . $code['date_code'];
           
            if (!isset($data->goods->$index)) return sendData(402, '有效期为' . $code['date_code'] . '的商品' . $code['type_code'] . '不在此单');
            if ($data->goods->$index->scanNumber + $code['number'] > $data->goods->$index->number) return sendData(402, '超出数量限制');
            $data->goods->$index->scanNumber += $code['number'];
            $boxes = $this->getArray($data->boxes);
            // dd($boxes);
            $otherBoxes = $this->getArray($data->otherBoxes);
            if ($code['unit'] == '箱') {
                if (in_array($code['box_code'], $boxes) || in_array($code['box_code'], $otherBoxes)) return sendData(402, '箱码不能重复');
                $boxes[] = $code['box_code'];
            } else {
                if (!in_array($code['box_code'], $otherBoxes)) $otherBoxes[] = $code['box_code'];
            }
            $data->boxes = $boxes;
            $data->otherBoxes = $otherBoxes;
            $number = 1;
            $arr = [
                $box_code . '-' . $product->PRODUCTCD . '-' . $date_code => [$number],
            ];
            $red = json_decode(Redis::get('fuhe::shunxu::' . $id));
            $k = $box_code . '-' . $product->PRODUCTCD . '-' . $date_code;
            if ($red) {
                if (array_key_exists($k, $red)) {
                    array_push($red->$k, $number);
                } else {
                    $red = json_decode(json_encode($red), true);
                    $red = array_merge($red, $arr);
                    Redis::set('fuhe::shunxu::' . $id, json_encode($red));
                }
            } else {
                Redis::set('fuhe::shunxu::' . $id, json_encode($arr));
            }
            $this->setToRedis($id, $data);
        }

        return sendData(200, '', $code);
    }

    // 解析效期
    private function parseXiaoqi($product, $str)
    {
        
        if (!$product->validity) return false;
        
        $one = substr($str, 0, 1);
        $days = (int)substr($str, 1) - 1;
     
        $month = $product->validity;
        
        $year = 0;
        if ($one > 3) {
            $year = 2010 + $one;
        } else {
            $year = 2020 + $one;
        }

        if ($days == 0) {
            $month -= 1;
        } else {
            
            $month += (int)date('m', strtotime(($year - 1) . '-12-31 +' . $days . 'days'));
           
        }

        $year += floor($month / 12);

        $month = $month % 12;
       
        if ($month == 0) {
            $year -= 1;
            $month = 12;
        }
       
        return sprintf('%d%02d', $year, $month);
    }

    public function getGoods(Request $request)
    {
        $id = $request->get('id');
        $res = $this->checkOrder($id);
        if ($res['status'] != 2) {
            return sendData(402, '请选择复核中的订单');
        }
        //创建复核的开始时间
        switch ($res['type']) {
            case 'o':
                $ordCode = DB::table('ord_out_dirt')->where('OrderNo', $id)->pluck('id');
                if (count($ordCode) != 0) {
                    $tag = DB::table('ord_out_dirt_tag')->whereIn('related_id', $ordCode)->pluck('fh_start');
                    if ($tag[0] == null) {
                        DB::table('ord_out_dirt_tag')->whereIn('related_id', $ordCode)->update(['fh_start' => date('Y-m-d H:i:s')]);
                    }
                }
                break;
            case 'm':
                $moveCode = DB::table('move_out_dirt')->where('MoveNo', $id)->pluck('id');
                if (count($moveCode) != 0) {
                    $tag = DB::table('move_out_dirt_tag')->whereIn('related_id', $moveCode)->pluck('fh_start');
                    if ($tag[0] == null) {
                        DB::table('move_out_dirt_tag')->whereIn('related_id', $moveCode)->update(['fh_start' => date('Y-m-d H:i:s')]);
                    }
                }
                break;
            case 'a':
                $adjCode = DB::table('adj_out_dirt')->where('AdjustNo', $id)->pluck('id');
                if (count($adjCode) != 0) {
                    $tag = DB::table('adj_out_dirt_tag')->whereIn('related_id', $adjCode)->pluck('fh_start');
                    if ($tag[0] == null) {
                        DB::table('adj_out_dirt_tag')->whereIn('related_id', $adjCode)->update(['fh_start' => date('Y-m-d H:i:s')]);
                    }
                }
                break;
            default:
                return sendData(402, '订单类型错误');
        }
        // Redis::del('fuhe::barcode::' . $id);
        // dd($this->getFromRedis($id));
     
        

        // 将扫码数据存入Redis
        if (!$this->getFromRedis($id)) {
            $goods_id = DB::table('goods_tag')->where('odd', $id)->pluck('goods_id');
            $goods = Goods::whereIn('id', $goods_id)->with('product')->get();
            $data = [];
            foreach ($goods as $item) {
                $product = Product::where('id',$item->product_id)->first();
                $key = $product->PRODUCTCD . '_' . $item->available_time;
                // $key = $item->product_id . '_' . $item->available_time;
                if (!isset($data[$key])) {
                    $data[$key] = [
                        'PRODUCTCD' => $item->product->PRODUCTCD,
                        'barCode' => $item->product->barCode,
                        'validity' => $item->product->validity,
                        'product_id' => $item->product_id,
                        'available_time' => $item->available_time,
                        'number' => $item->number,
                        'scanNumber' => 0
                    ];
                } else {
                    $data[$key]['number'] += $item->number;
                }
            }
            $redisData = [
                'goods' => $data,
                'codes' => [],
                'boxes' => [],
                'otherBoxes' => [],
            ];
            $this->setToRedis($id, $redisData);
        }

        return sendData();
    }

    public function checkJanhuoOrder(Request $request)
    {
        $id = $request->get('id');

        $result = DB::table('ganher')
        ->where('vbeln',$id)->orderBy('created_at', 'desc')->first();
        if($result->status != '未集货'){
            return sendData(402, '请选择拣货中的订单');
        }
        $res = $this->checkOrder($id);
        if ($res['status'] != 1) {
            return sendData(402, '请选择拣货中的订单');
        }
        //创建拣货的开始时间
        $moveCode = DB::table('move_out_dirt')->where('MoveNo', $id)->pluck('id');
        $adjCode = DB::table('adj_out_dirt')->where('AdjustNo', $id)->pluck('id');
        $ordCode = DB::table('ord_out_dirt')->where('OrderNo', $id)->pluck('id');
        if (count($moveCode) != 0) {
            $tag = DB::table('move_out_dirt_tag')->whereIn('related_id', $moveCode)->pluck('jh_start');
            if ($tag[0] == null) {
                DB::table('move_out_dirt_tag')->whereIn('related_id', $moveCode)->update(['jh_start' => date('Y-m-d H:i:s')]);
            }
        }
        if (count($adjCode) != 0) {
            $tag = DB::table('adj_out_dirt_tag')->whereIn('related_id', $adjCode)->pluck('jh_start');
            if ($tag[0] == null) {
                DB::table('adj_out_dirt_tag')->whereIn('related_id', $adjCode)->update(['jh_start' => date('Y-m-d H:i:s')]);
            }
        }
        if (count($ordCode) != 0) {
            $tag = DB::table('ord_out_dirt_tag')->whereIn('related_id', $ordCode)->pluck('jh_start');
            if ($tag[0] == null) {
                DB::table('ord_out_dirt_tag')->whereIn('related_id', $ordCode)->update(['jh_start' => date('Y-m-d H:i:s')]);
            }
        }
        return sendData(200, '');
    }

    private function checkOrder(string $id)
    {
        $goods_id = DB::table('goods_tag')->where('odd', $id)->pluck('goods_id');
        $goodsId = DB::table('goods')->whereIn('id', $goods_id)->get();
        if (count($goodsId) == 0) {
            return ['status' => 0, 'type' => ''];
        }
        $moveCode = DB::table('move_out_dirt')->where('MoveNo', $id)->pluck('id');
        $move = DB::table('move_out_dirt_tag')->whereIn('related_id', $moveCode)->pluck('status');
        if (count($move) != 0) {
            if ($move[0] == '拣货中') {
                return ['status' => 1, 'type' => 'm'];
            }

            if ($move[0] == '复核中') {
                return ['status' => 2, 'type' => 'm'];
            }
        }

        $adjCode = DB::table('adj_out_dirt')->where('AdjustNo', $id)->pluck('id');
        $adj = DB::table('adj_out_dirt_tag')->whereIn('related_id', $adjCode)->pluck('status');
        if (count($adj) != 0) {
            if ($adj[0] == '拣货中') {
                return ['status' => 1, 'type' => 'a'];
            }

            if ($adj[0] == '复核中') {
                return ['status' => 2, 'type' => 'a'];
            }
        }

        $ordCode = DB::table('ord_out_dirt')->where('OrderNo', $id)->pluck('id');
        $ord = DB::table('ord_out_dirt_tag')->whereIn('related_id', $ordCode)->pluck('status');
        if (count($ord) != 0) {
            if ($ord[0] == '拣货中') {
                return ['status' => 1, 'type' => 'o'];
            }

            if ($ord[0] == '复核中') {
                return ['status' => 2, 'type' => 'o'];
            }
        }

        return ['status' => 0, 'type' => ''];
    }

    public function getJianhuoStockList(Request $request)
    {
        $id = $request->get('id');
        $records = GoodsRecord::where(['odd' => $id, 'stock_no' => '复核区'])->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])->orderBy('origin_stock_no', 'asc')->get();
        if (count($records) == 0) return sendData(402, '拣货单错误，请刷新页面');
        $list = [];
        foreach ($records as $record) {
            if (!isset($list[$record->origin_stock_no]) || !$list[$record->origin_stock_no]) {
                $list[$record->origin_stock_no] = ['stock_no' => $record->origin_stock_no, 'status' => 0];
            }
        }

        //从redis获取已经扫描的库位,变更状态
        $stockData = json_decode(Redis::get('jianhuo::' . $id));
        if ($stockData) {
            foreach ($stockData as $stock_no => $data) {
                $list[$stock_no]['status'] = $data->status;
            }
        }
        return sendData(200, '', $list);
    }


    public function getJianhuoStock(Request $request)
    {
        $id = $request->get('id');
        $stock_no = $request->get('stock_no');
        if (!$id || !$stock_no) return sendData(402, '信息错误');

        $records = [];
        // 从redis获取数据
        $stockData = json_decode(Redis::get('jianhuo::' . $id));
        if ($stockData) {
            foreach ($stockData as $key => $data) {
                if ($stock_no == $key) {
                    $records = $data->data;
                    foreach ($records as $item) {
                        $item->product = Product::find($item->product_id);
                    }
                }
            }
            Redis::set('jianhuo::' . $id, json_encode($stockData));
        }
        if (!$records) {
            $records = GoodsRecord::where(['odd' => $id, 'stock_no' => '复核区', 'origin_stock_no' => $stock_no])
                ->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])
                ->with('product', 'unit')
                ->get();
            $surplus = 0;
            $box = 0;
            foreach ($records as $record) {
                foreach ($record->unit as $key => $val) {
                    if ($val->unit_name == '箱') {
                        if ($val->unit_name == '箱') {
                            $surplus = $record->number % $val->number;
                            if ($surplus == 0) {
                                $box = $record->number / $val->number;
                                $surplus = 0;
                            } else {
                                $box = ($record->number - $surplus) / $val->number;
                            }
                        }
                    } else {
                        continue;
                    }
                }
                $record->scanNumber = 0;
                $record->surplus = $surplus;
                $record->box = $box;
            }
           
        }
        $pre = $prf = [];
        $pre_key = '';
        foreach ($records as $k => $info) {
            $key = $info->product->PRODUCTCD. '-'.$info->available_time;
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
//             最后一条特殊处理
        $prf[] = $pre[$pre_key];
        return sendData(200, '', $prf);
    }

    public function doJianhuoStock(Request $request)
    {
        $records = $request->all();
        // 查看是否扫描完成
        $start = $count = 0;
        foreach ($records as $item) {
            if ($item['scanNumber'] > 0) $start = 1;
            if ($item['scanNumber'] == $item['number']) $count++;
        }


        $data = [
            'status' => $count == count($records) ? 2 : ($start == 0 ? 0 : 1),
            'data' => $records
        ];

        $stockData = json_decode(Redis::get('jianhuo::' . $records[0]['odd']));
        $stock_no = $records[0]['origin_stock_no'];
        if (!$stockData) {
            $stockData = new \stdClass();
        }
        $stockData->$stock_no = $data;

        Redis::set('jianhuo::' . $records[0]['odd'], json_encode($stockData));

        return sendData();
    }

    public function doJianhuo(Request $request)
    {
        $id = $request->get('id');
        try {
            DB::beginTransaction();
            if (!$id) {
                return sendData(402, '单号不能为空');
            }
            if (!$stockData = json_decode(Redis::get('jianhuo::' . $id))) {
                return sendData(402, '该单未开始拣货！');
            }

            $records = GoodsRecord::where(['odd' => $id, 'stock_no' => '复核区'])->whereIn('type', ['ord_out_dirt', 'move_out_dirt', 'adj_out_dirt'])->get();
            if (count($records) == 0) return sendData(402, '拣货单错误，请刷新页面');
            $type = '';
            $relatedIds = [];
            foreach ($records as $item) {
                $type = $item->type;
                $relatedIds[] = $item->related_id;
                $stock_no = $item->origin_stock_no;
                if (!isset($stockData->$stock_no) || $stockData->$stock_no->status != 2) {
                    return sendData(402, '未拣货完成，请先完成拣货');
                }
            }
            $res = DB::table($type . '_tag')->whereIn('related_id', $relatedIds)->update(['status' => '复核中', 'jh_end' => date('Y-m-d H:i:s')]);
            DB::table('ganher')->where('vbeln', $id)->update(['status' => 'App']);
            if (!$res) return sendData(402, '拣货失败');
            Redis::set('jianhuo::' . $id, null);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData();
    }

    // 从redis中获取数据
    private function getFromRedis($id)
    {
        return json_decode(Redis::get('fuhe::barcode::' . $id));
    }

    // 保存数据到redis
    private function setToRedis($id, $data)
    {
        if ($data) {
            Redis::set('fuhe::barcode::' . $id, json_encode($data));
        } else {
            Redis::del('fuhe::barcode::' . $id);
        }

        return true;
    }

    public function getProduct(Request $request)
    {
        $NewProductCd = $request->get('newProductCd');
        $product = Product::where('NewPRODUCTCD',$NewProductCd)->first();
        if(!$product){
            return sendData(402, '该产品不存在');
        }
        return sendData(200, '', $product->PRODUCTCD);
    }
}
