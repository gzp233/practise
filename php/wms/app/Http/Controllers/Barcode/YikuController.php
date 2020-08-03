<?php
/**
 * Created by Valley.
 * User: Valley
 * Date: 2019/05/08
 * Time: 15:48
 */
namespace App\Http\Controllers\Barcode;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Storage\Yiku;
use Illuminate\Support\Facades\Redis;
use App\Models\Storage\GoodsRecord;
use App\Models\Storage\Goods;
use Illuminate\Support\Facades\Cache;
use App\Models\Base\Product;

class YikuController extends BarcodeController
{
    /**
     * Create a new GoodsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->user = auth()->user();
    }

    // 获取未处理的移库单列表
    public function getYikuNos(Request $request)
    {
        $query = [
            ['status', '<>', 2],
            ['deal_user', '=', $this->user->id],
        ];
        $data = DB::table('yiku')
            ->select('yiku_no', 'source')
            ->groupBy('yiku_no', 'source')
            ->where($query)
            ->get();
        $list = [];
        foreach ($data as $item) {
            if ($this->getFromRedis($item->yiku_no)) {
                $list[] = ['yiku_no' => $item->yiku_no, 'status' => 1, 'source' => $item->source];
            } else {
                $list[] = ['yiku_no' => $item->yiku_no, 'status' => 0, 'source' => $item->source];
            }
        }
        return sendData(200, '', $list);
    }

    //根据移库单号获取移库列表
    public function getStockListByNo(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return sendData(402, '移库单号为空');
        $query = [
            ['deal_user', '=', $this->user->id],
            ['yiku_no', '=', $yiku_no],
            ['status', '<>', 2],
        ];
        $res = Yiku::where($query)->with('product')->get();
        if (!$res) return sendData(402, '该移库单不存在或已完成！');

        $originList = $toList = [];
        foreach ($res as $item) {
            if ($item->source == 1) return sendData(402, '入口错误！');
            if (!isset($originList[$item->origin_stock_no])) {
                $originList[$item->origin_stock_no] = ['stock_no' => $item->origin_stock_no, 'status' => 0];
            }
            if (!isset($toList[$item->stock_no])) {
                $toList[$item->stock_no] = ['stock_no' => $item->stock_no, 'status' => 0];
            }
        }

        $stockData = $this->getFromRedis($yiku_no);
        if ($stockData) {
            foreach ($stockData->origin as $key => $value) {
                $originList[$key]['status'] = $value->status;
            }
            foreach ($stockData->to as $key => $value) {
                $toList[$key]['status'] = $value->status;
            }
        } else {
            $stockData = [
                'origin' => [],
                'to' => [],
                'goodsList' => []
            ];
            foreach ($res as $item) {
                $index = $item->product_id.'_'.$item->available_time;
                if (isset($stockData['goodsList'][$index])) {
                    $stockData['goodsList'][$index]['number'] += $item->number;
                } else {
                    $stockData['goodsList'][$index] = [
                        'number' => $item->number,
                        'origin_number' => 0,
                        'to_number' => 0
                    ];
                }

                if (isset($stockData['origin'][$item->origin_stock_no]['data'][$index])) {
                    $stockData['origin'][$item->origin_stock_no]['data'][$index]['number'] += $item->number;
                } else {
                    $stockData['origin'][$item->origin_stock_no]['data'][$index] = [
                        'product_id' => $item->product_id,
                        'NewPRODUCTCD' => $item->product->NewPRODUCTCD,
                        'validity' => $item->product->validity,
                        'barCode' => $item->product->barCode,
                        'available_time' => $item->available_time,
                        'state_name' => $item->state_name,
                        'number' => $item->number,
                        'before' => 0,
                        'scanNumber' => 0
                    ];
                    $stockData['origin'][$item->origin_stock_no]['status'] = 0;
                }

                if (isset($stockData['to'][$item->stock_no]['data'][$index])) {
                    $stockData['to'][$item->stock_no]['data'][$index]['number'] += $item->number;
                } else {
                    $stockData['to'][$item->stock_no]['data'][$index] = [
                        'product_id' => $item->product_id,
                        'NewPRODUCTCD' => $item->product->NewPRODUCTCD,
                        'validity' => $item->product->validity,
                        'barCode' => $item->product->barCode,
                        'available_time' => $item->available_time,
                        'state_name' => $item->state_name,
                        'number' => $item->number,
                        'before' => 0,
                        'scanNumber' => 0
                    ];
                    $stockData['to'][$item->stock_no]['status'] = 0;
                }
            }
            $this->setToRedis($yiku_no, $stockData);
            Yiku::where($query)->update(['starttime' => date('Y-m-d H:i:s')]);
        }

        return sendData(200, '', ['origin' => $originList, 'to' => $toList]);
    }

    // 根据库位号，单号和type获取该库位移库信息
    public function getStock(Request $request)
    {
        $params = $request->all();
        if (!isset($params['yiku_no']) || !isset($params['stock_no'])) return sendData(402, '移库单号或库位号不能为空');
        if (!isset($params['type']) || !in_array($params['type'], ['origin', 'to'])) $params['type'] = 'origin';

        $data = [];
        // 从redis获取数据
        $stockData =  $this->getFromRedis($params['yiku_no']);
        $stock_no = $params['stock_no'];
        if ($params['type'] == 'origin') {
            if (isset($stockData->origin->$stock_no)) {
                foreach ($stockData->origin->$stock_no->data as $index => $item) {
                    $product = Product::find($item->product_id);
                    $item->barCode = $product->barCode;
                    $item->validity = $product->validity;
                    $data[$index] = $item;
                }
            }
        } else {
            if (isset($stockData->to->$stock_no)) {
                foreach ($stockData->to->$stock_no->data as $index => $item) {
                    $product = Product::find($item->product_id);
                    $item->barCode = $product->barCode;
                    $item->validity = $product->validity;
                    $data[$index] = $item;
                }
            }
        }
        $this->setToRedis($params['yiku_no'], $stockData);

        if (!$data) return sendData(402, '库位号错误！');

        return sendData(200, '', $data);
    }

    // 存储库位数据
    public function doStock(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        $stock_no = $request->get('stock_no');
        $params = $request->get('params');
        $type = $request->get('type');
        if (!$yiku_no || !$stock_no)  return sendData(402, '单号或库位号不能为空！');
        if (!$params)  return sendData(402, '提交数据不能为空');
        if (!$type || !in_array($type, ['origin', 'to'])) $type = 'origin';

        $stockData = $this->getFromRedis($yiku_no);
        // 查看是否扫描完成
        $start = $count = 0;
        $data = [];
        foreach ($params as $item) {
            if ($item['scanNumber'] > 0) {
                $start = 1;
                $index = $item['product_id'].'_'.$item['available_time'];
                if ($type == 'origin') {
                    $stockData->goodsList->$index->origin_number += $item['scanNumber'] - $item['before'];
                } else {
                    $stockData->goodsList->$index->to_number += $item['scanNumber'] - $item['before'];
                }
                if ($stockData->goodsList->$index->origin_number < $stockData->goodsList->$index->to_number) {
                    return sendData(402, '请先转出！');
                }
                $item['before'] = $item['scanNumber'];
                if ($item['scanNumber'] == $item['number']) $count++;
                if ($item['scanNumber'] > $item['number']) return sendData(402, '扫描数量不能超过实际数量');
            }
            $data['data'][$index] = $item;
        }

        $data['status'] = $count == count($params) ? 2 : ($start == 0 ? 0 : 1);
        if ($type == 'origin') {
            $stockData->origin->$stock_no = $data;
        } else {
            $stockData->to->$stock_no = $data;
        }
        $this->setToRedis($yiku_no, $stockData);

        return sendData();
    }

    // 提交任务
    public function submit(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) {
            return sendData(402, '单号不能为空');
        }
        if (!$stockData = $this->getFromRedis($yiku_no)) {
            return sendData(402, '该单不存在!');
        }

        $query = [
            ['yiku_no', '=', $yiku_no],
            ['status', '<>', 2],
            ['source', '=', 0],
        ];
        $count = Yiku::where($query)->count();
        if ($count == 0) return sendData(402, '移库单错误，请刷新页面');
        $stockData = $this->getFromRedis($yiku_no);
        foreach ($stockData->goodsList as $item) {
            if ($item->origin_number != $item->number || $item->origin_number != $item->to_number) {
                return sendData(402, '未完成扫码');
            }
        }
        try {
            DB::beginTransaction();
            $yikus = Yiku::where($query)->get();
            foreach ($yikus as $value) {
                $where = [
                    'product_id' => $value->product_id,
                    'state_name' => $value->state_name,
                    'stock_no' =>  $value->stock_no,
                    'available_time' => $value->available_time,
                ];
                $first = Goods::where($where)->first();
                if($first){
                    $first->number += $value->number;
                    $first->save();
                    Goods::where('id', $value->goods_id)->delete();
                }else{
                    Goods::where('id', $value->goods_id)->update(['stock_no' => $value->stock_no]);
                }
                GoodsRecord::create([
                    'product_id' => $value->product_id,
                    'state_name' => $value->state_name,
                    'CHARG' => '00',
                    'number' => $value->number,
                    'odd' => $yiku_no,
                    'stock_no' =>  $value->stock_no,
                    'origin_stock_no' => '移库区',
                    'type' => 'yiku',
                    'available_time' => $value->available_time,
                    'related_id' => 1,
                ]);
            }
            Yiku::where($query)->update(['status' => 2, 'endtime' => date('Y-m-d H:i:s')]);
            // 生成回传文件
            $genData = Yiku::where('yiku_no', $yiku_no)->whereRaw('origin_state_name<>state_name')->with('product')->get();
            if (count($genData) > 0) {
                $this->genTxt($genData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }

        $this->setToRedis($yiku_no, null);

        return sendData();
    }

    // 生成回传文件
    private function genTxt($data)
    {
        if (Cache::get('generate_file_gen') == 1) {
            $time = date('YmdHis', strtotime('+1second'));
        } else {
            Cache::put('generate_file_gen', 1, 0.1);
            $time = date('YmdHis');
        }
        $path = storage_path() . '/uploads/OUT/';
        $fileName = 'StcState7858' . $time . '.txt';
        $location = $path . $fileName;
        foreach ($data as $value) {
            $item = [
                'BLDAT' => date('Ymd'),
                'BUDAT' => date('Ymd'),
                'BKTXT' => date('Ymd') . rand(100000, 999999),
                'MATNR' => $value->product->NewPRODUCTCD,
                'WERKS' => '7858',
                'LGORT' => $value->origin_state_name,
                'CHARG' => '00',
                'UMLGO' => $value->state_name,
                'UMCHA' => '00',
                'BWART' => '311',
                'ERFMG' => $value->number,
                'ERFME' => 'EA',
                'SGTXT' => '',
            ];;
            if (!file_exists($location)) {
                file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
            }
            file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
        }
        $okName = 'LO0057858' . $time . '.ok';
        file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
    }

    //手机端发起任务
    public function createTask(Request $request)
    {
        $yiku_no = date('YmdHis') . rand(10, 99);
        $data = [
            'data' => new \stdClass(),
            'starttime' => date('Y-m-d H:i:s'),
            'checkList' => [],
            'goodsList' => [],
            'source' => 1,
            'status' => 0
        ];
        $this->setToRedis($yiku_no, $data, 3 * 24 * 3600);

        return sendData(200, '', $yiku_no);
    }

    //手机端获取已扫描的转出商品列表
    public function getOriginGoodsList(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if (!$stockData) {
            if (!$stockData) return sendData(402, '获取失败，单号错误');
        }
        if ($stockData->status != 0) return sendData(402, '状态错误');

        return sendData(200, '', $stockData->goodsList);
    }

    //手机端已扫描转出库位列表
    public function getOriginStockList(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        $list = [];
        if ($stockData) {
            if (isset($stockData->source) && $stockData->source != 1) return sendData(402, '入口错误');
            foreach ($stockData->data as $stock_no => $item) {
                $list[] = $stock_no;
            }
            if ($stockData->status != 0) return sendData(402, '状态错误！');
        }

        return sendData(200, '', $list);
    }

    // 手机端根据库位号获取该库位商品
    public function getOriginByStockNo(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        $stock_no = $request->get('stock_no');
        if (!$stock_no ||  !$yiku_no) return sendData(402, '库位号或单号不能为空');
        $data = [];
        $stockData = $this->getFromRedis($yiku_no);
        if ($stockData->status != 0) return sendData(402, '已完成移出操作，无法继续移出');
        if (isset($stockData->data->$stock_no)) {
            foreach ($stockData->data->$stock_no as $index => $item) {
                $product = Product::find($item->product_id);
                $item->barCode = $product->barCode;
                $item->validity = $product->validity;
                $data[$index] = $item;
            }
            
            $this->setToRedis($yiku_no, $stockData, 72 * 3600);
        }
        $checkList = [];
        foreach ($stockData->checkList as $key => $value) {
            $checkList[$key] = $value;
        }

        if (!$data) {
            // 库位也要检查
            $stockCheckList = [];
            $rs = Goods::where('stock_no', $stock_no)->with('product')->get();
            foreach ($rs as $item) {
                if (in_array($item->product_id, array_keys($checkList))) {
                    if ($checkList[$item->product_id] != $item->state_name) return sendData(402, '库位异常,存在不同状态商品');
                }
                if (in_array($item->product_id, array_keys($stockCheckList))) {
                    if ($stockCheckList[$item->product_id] != $item->state_name) return sendData(402, '库位异常,存在不同状态商品');
                } else {
                    $stockCheckList[$item->product_id] = $item->state_name;
                }
                if (isset($data[$item->product_id.'_'.$item->available_time])) {
                    $data[$item->product_id.'_'.$item->available_time]['number'] += $item->number;
                } else {
                    $data[$item->product_id.'_'.$item->available_time] = [
                        'product_id' => $item->product_id,
                        'NewPRODUCTCD' => $item->product->NewPRODUCTCD,
                        'PRODUCTCD' => $item->product->PRODUCTCD,
                        'validity' => $item->product->validity,
                        'barCode' => $item->product->barCode,
                        'available_time' => $item->available_time,
                        'state_name' => $item->state_name,
                        'number' => $item->number,
                        'before' => 0,
                        'scanNumber' => 0
                    ];
                }
            }
            if (empty($data)) return sendData(402, '该库位不存在或无商品');
        }
        
        return sendData(200, '', $data);
    }

    //手机端保存转出库位扫码
    public function stockOrigin(Request $request)
    {
        $stock_no = $request->get('stock_no');
        $yiku_no = $request->get('yiku_no');
        $params = $request->get('params');
        if (!$stock_no || !$yiku_no) return sendData(402, '库位号或单号不能为空');
        if (!$params) return sendData(402, '数据不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if ($stockData->status != 0) return sendData(402, '已完成移出操作，无法继续移出');
        $goodsList = $checkList = [];
        foreach ($stockData->checkList as $key => $value) {
            $checkList[$key] = $value;
        }
        foreach ($stockData->goodsList as $key => $value) {
            $goodsList[$key] = $value;
        }
        $count = 0;
        foreach ($params as $key => $item) {
            if ($item['scanNumber'] > $item['number']) return sendData(402, '扫描数量不能超过库存数量');
            if ($item['scanNumber'] == 0) $count++;
            if ($item['scanNumber'] > 0) {
                if (in_array($item['product_id'], array_keys($checkList))) {
                    if ($checkList[$item['product_id']] != $item['state_name']) return sendData(402, $item['product']['PRODCHINM'].'存在其他状态');
                } else {
                    $checkList[$item['product_id']] = $item['state_name'];
                }
            }

            if (isset($goodsList[$item['product_id'].'_'.$item['available_time']])) {
                $goodsList[$item['product_id'].'_'.$item['available_time']]->number +=  $item['scanNumber'] - $item['before'];
            } else {
                $goodsList[$item['product_id'].'_'.$item['available_time']] = [
                    'product_id' => $item['product_id'],
                    'NewPRODUCTCD' => $item['NewPRODUCTCD'],
                    'PRODUCTCD' => $item['PRODUCTCD'],
                    'validity' => $item['validity'],
                    'barCode' => $item['barCode'],
                    'available_time' => $item['available_time'],
                    'state_name' => $item['state_name'],
                    'number' => $item['scanNumber'],
                ];
            }
            $params[$key]['before'] = $item['scanNumber'];
        }
        if ($count == count($params)) return sendData(402, '扫描数据不能为空');
        $stockData->data->$stock_no = $params;
        $stockData->checkList = $checkList;
        $stockData->goodsList = $goodsList;
        $this->setToRedis($yiku_no, $stockData, 3 * 24 * 3600);

        return sendData();
    }

    //手机端完成移出操作
    public function submitOrigin(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if (!$stockData) return sendData(402, '单号错误');
        // 把扫描的数据整理出来
        if (!$stockData->data) return sendData(402, '提交数据不能为空');
        try {
            DB::beginTransaction();
            foreach ($stockData->data as $stock_no => $row) {
                $tmpData = $stockCheckList = [];
                $rs = Goods::where('stock_no', $stock_no)->with('product')->get();
                foreach ($rs as $v) {
                    if (in_array($v->product_id, array_keys($stockCheckList))) {
                        if ($stockCheckList[$v->product_id] != $v->state_name) return sendData(402, '库位异常,存在不同状态商品');
                    } else {
                        $stockCheckList[$v->product_id] = $v->state_name;
                    }
                    if (!isset($tmpData[$v->product_id.'_'.$v->state_name.'_'.$v->available_time])) {
                        $tmpData[$v->product_id.'_'.$v->state_name.'_'.$v->available_time]= [
                            'data' => [$v],
                            'total' => $v->number
                        ];
                    } else {
                        $tmpData[$v->product_id.'_'.$v->state_name.'_'.$v->available_time]['data'][] = $v;
                        $tmpData[$v->product_id.'_'.$v->state_name.'_'.$v->available_time]['total'] += $v->number;
                    }
                }
                foreach ($row as $item) {
                    if ($item->scanNumber <= 0) continue;
                    $index = $item->product_id.'_'.$item->state_name.'_'.$item->available_time;
                    // 验证库位状态
                    if (!isset($tmpData[$index]) || $item->number != $tmpData[$index]['total']) {
                        $stockData->data = new \stdClass();
                        $stockData->checkList = [];
                        $stockData->goodsList = [];
                        $this->setToRedis($yiku_no, $stockData,  3 * 24 * 3600);
                        throw new \Exception( '库存状态发生变化，请刷新页面重试');
                    }
                    // 开始分配
                    $total = $item->scanNumber;
                    foreach ($tmpData[$index]['data'] as $goods) {
                        if ($total == 0) break;
                        if ($total >= $goods->number) {
                            if ($goods->frozen_number == 0) {
                                $goods->delete();
                            } else {
                                $goods->number = 0;
                                $goods->save();
                            }
                            $total -= $goods->number;
                        } else {
                            $goods->number = $goods->number - $total;
                            $goods->save();
                            $total = 0;
                        }
                    }

                    $goodsData = [
                    'product_id' => $item->product_id,
                    'state_name' => $item->state_name,
                    'CHARG' => '00',
                    'number' => $item->scanNumber,
                    'available_time' => $item->available_time,
                    'real_number' => $item->scanNumber,
                    'frozen_number' => 0,
                    'stock_no' => '移库区',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $rc = Goods::insertGetId($goodsData);
                    $res = [
                        'odd'=>$yiku_no,
                        'goods_id'=>$rc,
                    ];
                    DB::table('goods_tag')->insert($res);
                    $other = [
                        'origin_stock_no' => $stock_no,
                        'type' => 'yiku',
                        'available_time' => $item->available_time,
                        'related_id' => 1,
                        'odd'=>$yiku_no,
                    ];
                    $goodsRecordData = array_merge($goodsData, $other);
                    unset($goodsRecordData['real_number'], $goodsRecordData['frozen_number']);
                    GoodsRecord::create($goodsRecordData);

                    $yikuData = [
                        'yiku_no' => $yiku_no,
                        'create_user' => $this->user->id,
                        'goods_id' => $rc,
                        'deal_user' => $this->user->id,
                        'product_id' => $item->product_id,
                        'origin_stock_no' => $stock_no,
                        'origin_state_name' => $item->state_name,
                        'origin_available_time' => $item->available_time,
                        'starttime' => $stockData->starttime,
                        'number' => $item->scanNumber,
                        'source' => 1,
                        'status' => 0
                    ];
                    Yiku::create($yikuData);
                }
            }
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        $this->setToRedis($yiku_no, null);

        return sendData();
    }

    //手机端已扫描转出库位列表
    public function getToStockList(Request $request)
    {
        $yiku_no = $request->get('yiku_no');

        if (!$yiku_no) return sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);

        $list = [];
        if ($stockData) {
            if (isset($stockData->source) && $stockData->source != 1) return sendData(402, '入口错误');
            foreach ($stockData->data as $stock_no => $item) {
                $list[] = $stock_no;
            }
        } else {
            $this->setGoodsData($yiku_no);
        }
        return sendData(200, '', $list);
    }

    private function setGoodsData($yiku_no)
    {
        $query = [
            ['yiku_no', '=', $yiku_no],
            ['source', '=', 1],
            ['status', '=', 0]
        ];
        $yikus = Yiku::where($query)->with('product')->get();
        if (!$yikus) throw new \Exception('该单不存在或者状态异常');
        $goodsList = $checkList = [];
        foreach ($yikus as $item) {
            $checkList[$item->product_id] = $item->origin_state_name;
            if (isset($goodsList[$item->product_id.'_'.$item->origin_available_time])) {
                $goodsList[$item->product_id.'_'.$item->origin_available_time]['number'] +=  $item->number;
            } else {
                $goodsList[$item->product_id.'_'.$item->origin_available_time] = [
                    'product_id' => $item->product_id,
                    'NewPRODUCTCD' => $item->product->NewPRODUCTCD,
                    'validity' => $item->product->validity,
                    'barCode' => $item->product->barCode,
                    'available_time' => $item->origin_available_time,
                    'state_name' => $item->origin_state_name,
                    'number' => $item->number,
                    'before' => 0,
                    'scanNumber' => 0
                ];
            }
        }
        $stockData = [
            'data' => new \stdClass(),
            'checkList' => $checkList,
            'goodsList' => $goodsList,
            'status' => 1,
            'source' => 1
        ];
        $this->setToRedis($yiku_no, $stockData);
    }

    //手机端获取已扫描的转出商品列表
    public function getGoodsList(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if (!$stockData) {
            if(!Yiku::where(['yiku_no' => $yiku_no, 'source' => 1, 'status' => 0])->count()) return sendData(402, '获取失败');
            $this->setGoodsData($yiku_no);
            $stockData = $this->getFromRedis($yiku_no);
            if (!$stockData) return sendData(402, '获取失败，单号错误');
        }
        $data = [];
        foreach ($stockData->goodsList as $index => $item) {
            $product = Product::where('id',$item->product_id)->first();
            $product = Product::find($item->product_id);
            $item->barCode = $product->barCode;
            $item->PRODUCTCD = $product->PRODUCTCD;
            $item->validity = $product->validity;
            $data[$index] = $item;
        }
        $this->setToRedis($yiku_no, $stockData);

        if ($stockData->status != 1) return sendData(402, '请先进行转出操作！');

        return sendData(200, '', $data);
    }

    // 手机端根据库位号获取该移入库位商品
    public function getToByStockNo(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        $stock_no = $request->get('stock_no');
        if (!$stock_no || !$yiku_no) return sendData(402, '库位号或单号不能为空');
        $data = [];
        $stockData = $this->getFromRedis($yiku_no);
        if ($stockData->status == 0) return sendData(402, '未完成移出操作');
        if (isset($stockData->data->$stock_no)) {
            $data = $stockData->data->$stock_no;
        }
        // 如果未扫描过，检查
        if (!$data) {
            $checkList = [];
            foreach ($stockData->checkList as $key => $value) {
                $checkList[$key] = $value;
            }
            // 库位也要检查
            $stockCheckList = [];
            $rs = Goods::where('stock_no', $stock_no)->with('product')->get();
            foreach ($rs as $item) {
                if (in_array($item->product_id, array_keys($stockCheckList))) {
                    if ($stockCheckList[$item->product_id] != $item->state_name) return sendData(402, '库位异常,存在不同状态商品');
                } else {
                    $stockCheckList[$item->product_id] = $item->state_name;
                }
                if (in_array($item->product_id, array_keys($checkList))) {
                    if ($checkList[$item->product_id] != $item->state_name) return sendData(402, '库位异常,存在不同状态商品');
                }
            }
        }
        
        return sendData(200, '', $data);
    }

    //手机端保存转出库位扫码
    public function stockTo(Request $request)
    {
        $stock_no = $request->get('stock_no');
        $yiku_no = $request->get('yiku_no');
        $params = $request->get('params');
        if (!$stock_no || !$yiku_no) return sendData(402, '库位号或单号不能为空');
        if (!$params) return sendData(402, '数据不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if ($stockData->status == 0) return sendData(402, '未完成移出操作');
        $stockList = [];
        if (isset($stockData->data->$stock_no)) {
            foreach ($stockData->data->$stock_no as $key => $item) {
                $stockList[$key] = $item;
            }
        }

        $scanCount = 0;
        foreach ($params as $item) {
            $key = $item['product_id'].'_'.$item['available_time'];
            $stockData->goodsList->$key->scanNumber += $item['scanNumber'] - $item['before'];
            if ($stockData->goodsList->$key->scanNumber > $stockData->goodsList->$key->number) {
                return sendData(402, '扫描数量不能超过移出数量');
            }
            $stockData->goodsList->$key->before= $item['scanNumber'];
            if(isset($stockList[$key])) {
                $stockList[$key]->scanNumber += $item['scanNumber'] - $item['before'];
                $scanCount += $stockList[$key]->scanNumber;
                if ($stockList[$key]->scanNumber == 0) unset($stockList[$key]);
            } else {
                $stockList[$key] = $item;
                $stockList[$key]['scanNumber'] = $item['scanNumber'] - $item['before'];
                $scanCount += $stockList[$key]['scanNumber'];
                if ($stockList[$key]['scanNumber'] == 0) unset($stockList[$key]);
            }
        }
        if ($scanCount == 0) {
            unset($stockData->data->$stock_no);
        } else {
            $stockData->data->$stock_no = $stockList;
        }

        $this->setToRedis($yiku_no, $stockData);

        return sendData();
    }

    //手机端提交移入库位
    public function submitTo(Request $request)
    {
        $yiku_no = $request->get('yiku_no');
        if (!$yiku_no) return sendData(402, '单号不能为空');
        $stockData = $this->getFromRedis($yiku_no);
        if (!$stockData) return sendData(402, '单号错误');
        if ($stockData->status != 1) return sendData(402, '状态错误,移出操作未完成');
        // 把扫描的数据整理出来
        if (!$stockData->data) return sendData(402, '提交数据不能为空');
        // 先验证是否全部扫描完成
        foreach ($stockData->goodsList as $item) {
            if ($item->scanNumber != $item->number) return sendData(402, '未移入全部商品');
        }

        //下面先整理数据，库位对应
        $yikuData = [];
        $query = [
            'yiku_no' => $yiku_no,
            'source' => 1
        ];
        $res = Yiku::where($query)->with('product')->get();
        if (!$res) return sendData(402, '移库单发生变化，请刷新重试');
        foreach ($res as $v) {
            $yikuData[$v->product_id.'_'.$v->origin_available_time][] = $v;
        }
        try {
            DB::beginTransaction();
            $goods_id = DB::table('goods_tag')->where('odd',$yiku_no)->pluck('goods_id');
            Goods::where(['stock_no' => '移库区'])->whereIn('id',$goods_id)->delete();
            DB::table('goods_tag')->where('odd',$yiku_no)->delete();
            foreach ($stockData->data as $stock_no => $row) {
                // 检查是否异常
                $stockCheckList = [];
                $rs = Goods::where('stock_no', $stock_no)->get();
                foreach ($rs as $v) {
                    if (in_array($v->product_id, array_keys($stockCheckList))) {
                        if ($stockCheckList[$v->product_id] != $v->state_name) return sendData(402, '库位异常,存在不同状态商品');
                    } else {
                        $stockCheckList[$v->product_id] = $v->state_name;
                    }
                }
                foreach ($row as $item) {
                    if ($item->scanNumber == 0) continue;
                    $goodsData = [
                        'product_id' => $item->product_id,
                        'state_name' => $item->state_name,
                        'CHARG' => '00',
                        'number' => $item->scanNumber,
                        'available_time' => $item->available_time,
                        'real_number' => $item->scanNumber,
                        'frozen_number' => 0,
                        'stock_no' => $stock_no
                    ];
                    $rc = Goods::create($goodsData);
                    $other = [
                        'origin_stock_no' =>'移库区',
                        'type' => 'yiku',
                        'available_time' => $item->available_time,
                        'related_id' => 1,
                    ];
                    $goodsRecordData = array_merge($goodsData, $other);
                    unset($goodsRecordData['real_number'], $goodsRecordData['frozen_number']);
                    GoodsRecord::create($goodsRecordData);

                    $index = $item->product_id.'_'.$item->available_time;
                    // 开始分配
                    $total = $item->scanNumber;
                    foreach ($yikuData[$index] as $k => $yiku) {
                        if ($total == 0) break;
                        if ($total >= $yiku->number) {
                            $yiku->delete();
                            unset($yikuData[$index][$k]);
                            $total -= $yiku->number;
                        } else {
                            $yiku->number = $yiku->number - $total;
                            $yiku->save();
                            $total = 0;
                        }

                        $yikuInsertData = [
                            'yiku_no' => $yiku_no,
                            'create_user' => $this->user->id,
                            'goods_id' => $rc->id,
                            'deal_user' => $this->user->id,
                            'product_id' => $item->product_id,
                            'origin_stock_no' => $yiku->origin_stock_no,
                            'origin_state_name' => $yiku->origin_state_name,
                            'origin_available_time' => $yiku->origin_available_time,
                            'stock_no' => $stock_no,
                            'state_name' => $item->state_name,
                            'available_time' => $item->available_time,
                            'starttime' => $yiku->starttime,
                            'endtime' => date('Y-m-d H:i:s'),
                            'number' => $item->scanNumber,
                            'source' => 1,
                            'status' => 2
                        ];
                        Yiku::create($yikuInsertData);
                    }
                }
            }
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        $this->setToRedis($yiku_no, null);

        return sendData();
    }

    // 从redis中获取数据
    private function getFromRedis($id)
    {
        return json_decode(Redis::get('yiku::barcode::'.$id));
    }

    // 保存数据到redis
    private function setToRedis($id, $data, $time = 0)
    {
        if ($data) {
            if ($time) {
                Redis::setex('yiku::barcode::'.$id, $time, json_encode($data));
            } else {
                Redis::set('yiku::barcode::'.$id, json_encode($data));
            }
        } else {
            Redis::del('yiku::barcode::'.$id);
        }

        return true;
    }
}
