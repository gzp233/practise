<?php

namespace App\Http\Controllers\Storage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Storage\MoveRolls;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\Cache;


class MoveRollsController extends Controller
{
    protected $rules = [];
    /**
     * Create a new InstorageProcessController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $query = [];
        $limit = !empty($params['limit']) ? $params['limit'] : 20;
        if (!empty($params['PRODCHINM'])) $query[] = ['p.PRODCHINM', 'like', '%' . $params['PRODCHINM'] . '%'];
        if (!empty($params['NewPRODUCTCD'])) $query[] = ['p.NewPRODUCTCD',  'like','%'. $params['NewPRODUCTCD']];
        if (!empty($params['PRODUCTCD'])) $query[] = ['p.PRODUCTCD',  'like','%'. $params['PRODUCTCD']];
        if (!empty($params['state_name'])) $query[] = ['g.state_name',  'like','%'. $params['state_name']];
        if (!empty($params['CHARG'])) $query[] = ['g.CHARG',  'like','%'. $params['CHARG']];
        if (!empty($params['stock_no'])) $query[] = ['g.stock_no',  'like','%'. $params['stock_no']];
         $query[] = ['g.stock_no',  '<>', '复核区'];
        $data = DB::table('goods as g')
            ->leftJoin('product as p', 'g.product_id', '=', 'p.id')
            ->select('p.id', DB::raw('sum(g.number) as total'), 'p.PRODCHINM', 'p.PRODUCTCD', 'p.NewPRODUCTCD', DB::raw('group_concat(g.id) as goods_ids'))
            ->where($query)
            ->whereNotIn('g.state_name', ['C301', 'C302', '加工完成'])
            ->groupBy('product_id');
        $data = $data->paginate($limit);

        return sendData(200, '', $data);
    }

    public function getGoodsByIds(Request $request)
    {
        $params = $request->all();
        if (empty($params)) return sendData(402, 'ID为空');
        $ids = [];
        foreach ($params as $item) {
            $tmp = explode(',', $item);
            foreach ($tmp as $v) {
                $ids[] = $v;
            }
        }
        $goods = Goods::whereIn('id', $ids)->where('stock_no','<>','复核区')->with(['product'])->get();

        return sendData(200, '', $goods);
    }

    /* 上架 */
    public function stockIn(Request $request)
    {
        $params = $request->all();
        if (empty($params)) {
            return sendData(402, '未选择入库商品！');
        }
        $process = [];
        foreach ($params as $item) {
            if (empty($item['act_stock_no'])) return sendData(402, '库位必选！');
            if (empty($item['act_state_name'])) return sendData(402, '状态必填！');
            if (empty($item['act_number'])) return sendData(402, '数量必填！');
            if (isset($process[$item['product_id']])) {
                $process[$item['product_id']]['number'] += $item['act_number'];
            } else {
                $process[$item['product_id']] = ['number' => $item['act_number']];
            }
        }

        try {
            // 开启事务
            DB::beginTransaction();
            $exists = [];
            //插入InstorageProcess表
            foreach ($params as $key => $item) {
                $data[] = [
                        'BLDAT' => date('Ymd', strtotime($item['created_at'])),
                        'BUDAT' => date('Ymd'),
                        'BKTXT' => date('Ymd') . rand(100000,999999),
                        'MATNR' => $item['product']['NewPRODUCTCD'],
                        'WERKS' => '7858',
                        'LGORT' => $item['state_name'],
                        'CHARG' => '00',
                        'UMLGO' => $item['act_state_name'],
                        'UMCHA' => '00',
                        'BWART' => '311',
                        'ERFMG' => $item['act_number'],
                        'ERFME' => 'EA',
                        'SGTXT' => '',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                // 生成TXT和插入
//                    $date['created_at'] = date('Y-m-d H:i:s');
//                    $id = MoveRolls::insertGetId($date);
//                foreach ($process as $product_id => $value) {
//                    $process[$product_id]['insertId'] = $id;
//                }
            }
            $this->genTxt('stc_state', $data);
            foreach ($data as $key=> $item) {
                $id = MoveRolls::insertGetId($item);
                foreach ($process as $product_id => $value) {
                    $process[$product_id]['insertId'] = $id;
                }
            }
            // 循环库位
            foreach ($params as $item) {
                // 插入goods表和副表
                $goods = Goods::find($item['id']);
                if (!in_array($goods->id, $exists)) {
                    if ($goods->number != $item['number']) throw new \Exception('库存数量发生变化，无法入库！');
                    $exists[] = $goods->id;
                }

                if ($goods->number == $item['act_number']) {
                    $goods->delete();
                } else {
                    $goods->number -= $item['act_number'];
                    $goods->save();
                }
                $goodsData = [
                    'product_id' => $item['product']['id'],
                    'stock_no' => $item['act_stock_no'],
                    'state_name' => $item['act_state_name'],
                    'CHARG' => $item['CHARG'],
                    'odd' => $item['odd'],
                    'available_time' => $item['available_time'],
                ];
                dd($goodsData);
                $goodsS = Goods::where($goodsData)->first();
                if ($goodsS) {
                    $goodsS->number += $item['act_number'];
                    $goodsS->save();
                } else {
                    $goodsData['number'] = $item['act_number'];
                    $goodsData['created_at'] = $goodsData['updated_at'] = date('Y-m-d H:i:s');
                    Goods::insert($goodsData);
                }

                $goodsData['number'] = $item['act_number'];
                $goodsData['created_at'] = $goodsData['updated_at'] = date('Y-m-d H:i:s');
                $goodsData['origin_stock_no'] = $item['stock_no'];
                $goodsData['type'] = 'move_rolls';
                $goodsData['related_id'] = $process[$item['product']['id']]['insertId'];
                GoodsRecord::insert($goodsData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData(200, 'ok');
    }
    private function genTxt($type, $data)
    {
        if (Cache::get('generate_file_gen') == 1) {
            $time = date('YmdHis',strtotime('+1second'));
        } else {
            Cache::put('generate_file_gen', 1, 0.1);
            $time = date('YmdHis');
        }
        $path = storage_path() . '/uploads/OUT/';
        switch ($type) {
            case 'stc_state':
                $fileName = 'StcState7858' . $time . '.txt';
                $location = $path . $fileName;
                foreach ($data as $value) {
                    $item = $this->getOtherData($type, $value);
                    if (!file_exists($location)) {
                        file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
                    }
                    file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
                }
                $okName = 'LO0057858' . $time . '.ok';
                file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
                break;
        }
        return null;
    }

    private function getOtherData($type, $order)
    {
        $arr = [];
        switch ($type) {
            case 'stc_state':
                $arr = [
                    'BLDAT' => date('Ymd', strtotime($order['created_at'])),
                    'BUDAT' => date('Ymd'),
                    'BKTXT' => date('Ymd') . rand(100000,999999),
                    'MATNR' => $order['MATNR'],
                    'WERKS' => '7858',
                    'LGORT' => $order['LGORT'],
                    'CHARG' => '00',
                    'UMLGO' => $order['UMLGO'],
                    'UMCHA' => '00',
                    'BWART' => '311',
                    'ERFMG' => $order['ERFMG'],
                    'ERFME' => 'EA',
                    'SGTXT' => '',
                ];
                break;
        }
        return $arr;
    }
}