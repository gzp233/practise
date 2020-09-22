<?php

/**
 * Created by Valley.
 * User: Valley
 * Date: 2019/05/08
 * Time: 15:48
 */

namespace App\Http\Controllers\Barcode;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Outstorage\AntiCode;
use App\Models\Outstorage\MoveOut;
use App\Models\Outstorage\Adjust;
use App\Models\Outstorage\SalesOut;
use Illuminate\Support\Facades\Redis;
use App\Jobs\SendAntiCode;

class FangchuanhuoController extends BarcodeController
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

    // 获取进行中，或失败的防串货单
    public function index(Request $request)
    {
        $res1 = DB::table('anti_code')
            ->where('status', 0)
            ->select('CUSTOMERNAME', 'SHIPMENTID')
            ->groupBy('SHIPMENTID', 'CUSTOMERNAME')
            ->get();
        $res2 = DB::table('anti_code')
            ->where('status', 1)
            ->whereNotNull('error')
            ->select('CUSTOMERNAME', 'SHIPMENTID')
            ->groupBy('SHIPMENTID', 'CUSTOMERNAME')
            ->get();
        $data = $returns = [];
        $exists = [];
        foreach ($res1 as $item) {
            $item->status = '发送中';
            if (!in_array($item->SHIPMENTID, $exists)) {
                $exists[] = $item->SHIPMENTID;
            }
            $data[] = $item;
        }
        foreach ($res2 as $item) {
            if (in_array($item->SHIPMENTID, $exists)) continue;
            $item->status = '失败';
            $returns[] = $item;
        }
        return sendData(200, '', array_merge($returns, $data));
    }

    private function insertCode($c, $item, $id)
    {
        $data = [
            'AUTHCODE' => 'JFD1fdsa867',
            'SHIPMENTID' => $id,
            'PRODUCTNAME' => $item['PRODUCTNAME'],
            'PRODUCTCODE' => $item['NewProductCd'],
            'SHIPTIME' => date('Y-m-d H:i:s'),
            'starttime' => Redis::get('fangchuanhuo::' . $id),
            'deal_user' => $this->user->id,
            'CUSTOMER' => $item['CUSTOMER'],
            'CUSTOMERNAME' => $item['CUSTOMERNAME'],
            'FROM' => 'SH世源浦东仓',
            'QRCODE' => $c['code'],
            'box_code' => $c['box_code'],
            'type_code' => $c['type_code'],
            'UNIT' => $c['type'],
            'NUM' => $c['number'],
            'status' => 0,
        ];
        if (!AntiCode::create($data)) throw new \Exception('防串货码插入失败');

        return true;
    }

    // 检查防串货单号是否可用，并返回type
    public function checkFangchuanhuoOrder(Request $request)
    {
        $id = $request->get('id');
        if (!$id) return sendData(402, '单号不能为空');

        $data = MoveOut::where('MoveNo', $id)
            ->whereHas('tag', function ($q) {
                $q->where('status', '待发运');
            })
            ->whereHas('product', function ($q) {
                $q->where('is_need_code', '是');
            })
            ->with(['tag', 'product'])
            ->get();
        if (count($data) > 0) {
            $res = $this->getData($id, 'm');
            if ($res['status'] == 2) {
                return sendData(402, $res['message']);
            }
            return sendData(200, '', 'm');
        }

        $data = SalesOut::where('OrderNo', $id)
            ->whereHas('tag', function ($q) {
                $q->where('status', '待发运');
            })
            ->whereHas('product', function ($q) {
                $q->where('is_need_code', '是');
            })
            ->with(['tag', 'product'])
            ->get();
        if (count($data) > 0) {
            $res = $this->getData($id, 'o');
            if ($res['status'] == 2) {
                return sendData(402, $res['message']);
            }
            return sendData(200, '', 'o');
        }

        $data = Adjust::where('AdjustNo', $id)
            ->whereHas('tag', function ($q) {
                $q->where('status', '待发运');
            })
            ->whereHas('product', function ($q) {
                $q->where('is_need_code', '是');
            })
            ->with(['tag', 'product'])
            ->get();

        if (count($data) > 0) {
            $res = $this->getData($id, 'a');
            if ($res['status'] == 2) {
                return sendData(402, $res['message']);
            }
            return sendData(200, '', 'a');
        }

        return sendData(402, '单号错误或无防串货商品');
    }

    public function submit(Request $request)
    {
        $id = $request->get('id');
        $codes = $request->get('data');
        $type = $request->get('type');
        if (!$id || !$codes) return sendData(402, 'ID或防串货码不能为空');
        $goods = $this->getGoodsByIdAndType($id, $type);
        if (count($goods) == 0) return sendData(402, 'ID或类型错误');
        $goodsData = $this->collectGoods($goods, $type);
        $codeData = [];
        foreach ($codes as $code) {
            if (isset($codeData[$code['NewProductCd']])) {
                $codeData[$code['NewProductCd']]['code'][] = $code;
                $codeData[$code['NewProductCd']]['total'] += $code['number'];
            } else {
                $codeData[$code['NewProductCd']] = [
                    'code' => [$code],
                    'total' => $code['number']
                ];
            }
        }

        $antis = AntiCode::where('SHIPMENTID', $id)->with('product')->get();
        if (count($antis) > 0) {
            // 扫描错误代码
            $tmp = [];
            foreach ($antis as $anti) {
                if ($anti->status == 0 || ($anti->status == 1 && !$anti->error)) {
                    if (isset($tmp[$anti->PRODUCTCODE])) {
                        $tmp[$anti->PRODUCTCODE] += $anti->NUM;
                    } else {
                        $tmp[$anti->PRODUCTCODE] = $anti->NUM;
                    }
                }
            }
            foreach ($goodsData as $key => $item) {
                if (isset($tmp[$item['NewProductCd']])) {
                    $goodsData[$key]['number'] -= $tmp[$item['NewProductCd']];
                    if ($goodsData[$key]['number'] == 0) unset($goodsData[$key]);
                }
            }
            if (empty($goodsData)) return sendData(402, '该单已扫完');
            try {
                DB::beginTransaction();
                foreach ($goodsData as $item) {
                    if (isset($codeData[$item['NewProductCd']])) {
                        if ($codeData[$item['NewProductCd']]['total'] != $item['number']) {
                            throw new \Exception('数量错误，请重试');
                        } else {
                            foreach ($codeData[$item['NewProductCd']]['code'] as $c) {
                                $this->insertCode($c, $item, $id);
                            }
                        }
                    } else {
                        throw new \Exception('扫码不全');
                    }
                }
                // 删除失败的
                AntiCode::where(['SHIPMENTID' => $id, 'status' => 1])->whereNotNull('error')->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return sendData(402, $e->getMessage());
            }
        } else {
            // 如果第一次扫码
            if (count($goodsData) != count($codeData)) return sendData(402, '产品未扫描完整');
            try {
                DB::beginTransaction();
                foreach ($goodsData as $item) {
                    if (isset($codeData[$item['NewProductCd']])) {
                        if ($codeData[$item['NewProductCd']]['total'] != $item['number']) {
                            throw new \Exception('数量错误，请重试');
                        } else {
                            foreach ($codeData[$item['NewProductCd']]['code'] as $c) {
                                $this->insertCode($c, $item, $id);
                            }
                        }
                    } else {
                        throw new \Exception('扫码不全');
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return sendData(402, $e->getMessage());
            }
        }
        // 开始执行
        SendAntiCode::dispatch();
        Redis::set('set::' . $id, null);
        return sendData();
    }

    private function getGoodsByIdAndType($id, $type)
    {
        $map = [
            'm' => ['model' => new MoveOut(), 'column' => 'MoveNo', 'with' => ['tag', 'product']],
            'o' => ['model' => new SalesOut(), 'column' => 'OrderNo', 'with' => ['tag', 'product', 'customer']],
            'a' => ['model' => new Adjust(), 'column' => 'AdjustNo', 'with' => ['tag', 'product', 'customer']],
        ];

        $goods = $map[$type]['model']::where($map[$type]['column'], $id)
            ->whereHas('tag', function ($q) {
                $q->where('status', '待发运');
            })
            ->whereHas('product', function ($q) {
                $q->where('is_need_code', '是');
            })
            ->with($map[$type]['with'])
            ->get();

        return $goods;
    }

    private function collectGoods($goods, $type)
    {
        $goodsData = [];
        foreach ($goods as $item) {
            if (!$item->product->barCode) {
                throw new \Exception($item->product->PRODUCTCD . '支码不能为空！');
            }
            $c = [
                'NewProductCd' => $item->product->PRODUCTCD,
                'barCode' => $item->product->barCode,
                'number' => $item->tag->number,
                'PRODUCTNAME' => $item->product->PRODCHINM,
                'total' => 0,
                'x_num' => 0,
                'h_num' => 0,
            ];
            if ($type == 'm') {
                $c['CUSTOMER'] = $item->MovToCD;
                $c['CUSTOMERNAME'] = $item->MovToCD;
            } elseif ($type == 'a') {
                $c['CUSTOMER'] = $item->CustomerCD;
                $c['CUSTOMERNAME'] = $item->customer->ShopSignNM;
            } elseif ($type == 'o') {
                $c['CUSTOMER'] = $item->CustomerCd;
                $c['CUSTOMERNAME'] = $item->customer->ShopSignNM;
            } else {
                throw new \Exception('订单类型错误');
            }
            if ($item->product->units) {
                foreach ($item->product->units as $v) {
                    if ($v->unit_name == '箱') $c['x_num'] = $v->number;
                    if ($v->unit_name == '盒') $c['h_num'] = $v->number;
                }
            }
            $goodsData[] = $c;
        }

        return $goodsData;
    }

    private function getData($id, $type)
    {
        $goods = $this->getGoodsByIdAndType($id, $type);
        $goodsData = $this->collectGoods($goods, $type);
        foreach ($goodsData as $c) {
            if ($c['x_num'] == 0 || $c['h_num'] == 0)  return ['status' => 2, 'message' => '产品' . $c['NewProductCd'] . '无箱规！'];
        }
        if (!Redis::get('fangchuanhuo::' . $id)) {
            Redis::set('fangchuanhuo::' . $id, date('Y-m-d H:i:s'));
        }

        $status = 0;
        $fails = [];
        $antis = AntiCode::where('SHIPMENTID', $id)->with('product')->get();
        if (count($antis) > 0) {
            $status = 1;
            $tmp = [];
            foreach ($antis as $anti) {
                if ($anti->status == 1 && $anti->error) {
                    $fails[] = $anti;
                } else {
                    if (isset($tmp[$anti->PRODUCTCODE])) {
                        $tmp[$anti->PRODUCTCODE] += $anti->NUM;
                    } else {
                        $tmp[$anti->PRODUCTCODE] = $anti->NUM;
                    }
                }
            }
            foreach ($goodsData as $key => $item) {
                if (isset($tmp[$item['NewProductCd']])) {
                    $goodsData[$key]['number'] -= $tmp[$item['NewProductCd']];
                    if ($goodsData[$key]['number'] == 0) unset($goodsData[$key]);
                }
            }
            if (empty($goodsData)) return ['status' => 2, 'message' => '该单已扫完'];
        }

        return ['status' => $status, 'data' => $goodsData, 'fails' => $fails];
    }

    // 根据单号获取数据
    public function getByOrderNo(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        if (!$id || !$type || !in_array($type, ['o', 'm', 'a'])) return sendData(402, '单号或type错误');
        $data = $this->getData($id, $type);
        if ($data['status'] == 2) {
            return sendData(402, $data['message']);
        }
        $tmp = [];
        foreach ($data['data'] as $item) {
            $tmp[] = $item;
        }
        $data['data'] = $tmp;
        $data['reids'] = json_decode(Redis::get('set::' . $id))=== null ? [] : json_decode(Redis::get('set::' . $id));
        return sendData(200, '', $data);
    }
    public function setRedis(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        Redis::set('set::' . $id, json_encode($data));
        return sendData(200, '');
    }
}
