<?php

namespace App\Http\Controllers\Storage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Storage\MoveStock;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Goods;
use App\Models\Storage\GoodsRecord;
use App\Models\Base\Product;
use PhpOffice\PhpWord\TemplateProcessor;



class MoveStockController extends Controller
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

            foreach ($process as $product_id => $value) {
                $id = MoveStock::insertGetId([
                    'product_id' => $product_id,
                    'number' => $value['number'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $process[$product_id]['insertId'] = $id;
            }

            // 循环库位
            foreach ($params as $item) {
//                if($item['id'] == '12631'){
//                    continue;
//                }
                // 插入goods表和副表
                $goods = Goods::find($item['id']);
//                if (!in_array($goods->id, $exists)) {
//                    if ($goods->number != $item['number']) throw new \Exception('库存数量发生变化，无法入库！');
//                    $exists[] = $goods->id;
//                }

                if ($goods->number == $item['act_number']) {
                    $goods->delete();
                } else {
                    $goods->number -= $item['act_number'];
                    $goods->save();
                }
                $goodsData = [
                    'product_id' => $item['product']['id'],
                    'stock_no' => $item['act_stock_no'],
                    'state_name' => $item['state_name'],
                    'CHARG' => $item['CHARG'],
                    'odd' => $item['odd'],
                    'available_time' => date('Ym', strtotime($item['act_available_time'])),
                    'odd' => $item['odd'],
                ];
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
                $goodsData['type'] = 'move_stock';
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

}