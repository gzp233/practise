<?php

namespace App\Http\Controllers\Outstorage;

use App\Models\Outstorage\Adjust;
use App\Models\Outstorage\MoveOut;
use App\Models\Outstorage\SalesOut;
use App\Models\Outstorage\SalesOutTag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Outstorage\OrderOutEnsure;
use App\Models\Storage\GoodsRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OrdOutEnsureController extends OutBaseController
{
    protected $rules = [];

    private $map = [
        'move_out_dirt' => 'OutStcNo',
        'adj_out_dirt' => 'OutStcNo',
        'ord_out_dirt' => 'OutStcNo',
    ];

    /**
     * Create a new OrdOutEnsureController instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('refresh.token');
    }

    /**
     * 分页获取接受入库确认
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $params = [];
        if ($request->get('PRODUCTCD')) $params[] = ['PRODUCTCD', 'like', '%' . $request->get('PRODUCTCD') . '%'];

        $data = OrderOutEnsure::where($params)->orderBy($sort, 'desc')->with(['product'])->paginate($limit);

        return sendData(200, '', $data);
    }

    public function moveByNo(Request $request)
    {
        try {
            // 开启事务
            DB::beginTransaction();
            $outStcNo = $request->get('id');
            $orders = MoveOut::where('OutStcNo', $outStcNo)->get()->toArray();
            $tag = DB::table('move_out_dirt_tag')->where('related_id',$orders[0]['id'])->get()->toArray();
            if($tag[0]->status =='已回传'){
                return sendData(402, '该订单已回传');
            }

            // 生成TXT和插入
            $this->genTxt('move_out_dirt', $orders);
            foreach ($orders as $key => $item) {
                $arr = [
                    'VBELN' => $item['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $item['LineNo'],
                    'MATNR' => $item['NewProductCD'],
                    'AdmQnty' => $item['MovAdmQnty'],
                    'OrdTyep' => $item['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $item['MovFrmCD'],
                    'LGORT' => $item['FineFlg'],
                    'PRODUCTCD' => $item['ProductCD'],
                ];
                $arr['created_at'] = date('Y-m-d H:i:s');
                OrderOutEnsure::Insert($arr);
                $data = ['status'=>'已回传'];
                DB::table('move_out_dirt_tag')->where('related_id',$item['id'])->update($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }


        return sendData(200, 'ok!');
    }

    public function generate(Request $request)
    {
        try {
            //开启事务
            DB::beginTransaction();
            $outStcNo = $request->get('id');
            $orders = SalesOut::where('OutStcNo', $outStcNo)->get()->toArray();
            $tag = DB::table('ord_out_dirt_tag')->where('related_id',$orders[0]['id'])->get()->toArray();
            if($tag[0]->status =='已回传'){
                return sendData(402, '该订单已回传');
            }
            $this->genTxt('ord_out_dirt', $orders);
            foreach ($orders as $key => $item) {
                $arr = [
                    'VBELN' => $item['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $item['LineNo'],
                    'MATNR' => $item['NewProductCd'],
                    'AdmQnty' => $item['AdmQnty'],
                    'OrdTyep' => $item['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $item['PlaceCD'],
                    'LGORT' => $item['FineFlg'],
                    'PRODUCTCD' => $item['ProductCd'],
                ];
                $arr['created_at'] = date('Y-m-d H:i:s');
                OrderOutEnsure::Insert($arr);
                $data = ['status'=>'已回传'];
                DB::table('ord_out_dirt_tag')->where('related_id',$item['id'])->update($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData(200, 'ok!');
    }

    public function adjustNo(Request $request)
    {
        try {
            //开启事务
            DB::beginTransaction();
            $outStcNo = $request->get('id');
            $orders = Adjust::where('OutStcNo', $outStcNo)->get()->toArray();
            $tag = DB::table('adj_out_dirt_tag')->where('related_id',$orders[0]['id'])->get()->toArray();
            if($tag[0]->status =='已回传'){
                return sendData(402, '该订单已回传');
            }
            // 生成TXT和插入
            $this->genTxt('adj_out_dirt', $orders);
            foreach ($orders as $key => $item) {
                $arr = [
                    'VBELN' => $item['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $item['LineNo'],
                    'MATNR' => $item['NewProductCd'],
                    'AdmQnty' => $item['AdjustQnty'],
                    'OrdTyep' => $item['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $item['PlaceCD'],
                    'LGORT' => $item['FineFlg'],
                    'PRODUCTCD' => $item['ProductCd'],
                ];
                $arr['created_at'] = date('Y-m-d H:i:s');
                OrderOutEnsure::Insert($arr);
                $data = ['status'=>'已回传'];
                DB::table('adj_out_dirt_tag')->where('related_id',$item['id'])->update($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return sendData(200, 'ok!');
    }


    public function getByNo(Request $request)
    {
        $outStcNo = $request->get('id');
        $type = $request->get('type');
        if (!$outStcNo) {
            return sendData(402, '出库单号为空');
        }
        $ids = DB::table($type)->where($this->map[$type], $outStcNo)->pluck('id');
        $where = [
            'type' => $type,
            'stock_no' => '复核区'
        ];
        $orders = GoodsRecord::where($where)->whereIn('related_id', $ids)->with(['product'])->get();
        if (count($orders) == 0) return sendData(402, '获取失败');

        return sendData(200, '', $orders);
    }


    private function genTxt($type, $data)
    {
        if (Cache::get('generate_file_lock') == 1) {
            $time = date('YmdHis',strtotime('+1second'));
        } else {
            Cache::put('generate_file_lock', 1, 0.1);
            $time = date('YmdHis');
        }
        $path = storage_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'OUT' . DIRECTORY_SEPARATOR;
        switch ($type) {
            case 'move_out_dirt':
                $fileName = 'OrdOutEnsure7858' . $time . $data['0']['OutStcNo'] . '.txt';
                $location = $path . $fileName;
                foreach ($data as $value) {
                    $item = $this->getOtherData($type, $value);
                    if (!file_exists($location)) {
                        file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
                    }
                    file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
                }
                $okName = 'LO0067858' . $time . '.ok';
                file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
                break;
            case 'ord_out_dirt':
                $fileName = 'OrdOutEnsure7858' . $time . $data['0']['OutStcNo'] . '.txt';
                $location = $path . $fileName;
                foreach ($data as $value) {
                    $item = $this->getOtherData($type, $value);
                    if (!file_exists($location)) {
                        file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
                    }
                    file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
                }
                $okName = 'LO0067858' . $time . '.ok';
                file_put_contents($path . $okName, $fileName . "\x08" . filesize($location));
                break;
            case 'adj_out_dirt':
                $fileName = 'OrdOutEnsure7858' . $time . $data['0']['OutStcNo'] . '.txt';
                $location = $path . $fileName;
                foreach ($data as $value) {
                    $item = $this->getOtherData($type, $value);
                    if (!file_exists($location)) {
                        file_put_contents($location, implode("\x08", array_keys($item)), FILE_APPEND);
                    }
                    file_put_contents($location, "\r\n" . implode("\x08", array_values($item)), FILE_APPEND);
                }
                $okName = 'LO0067858' . $time . '.ok';
                file_put_contents($path . $okName, $fileName . "\x08" . filesize($location), LOCK_EX);
                break;
        }
        return null;
    }

    private function getOtherData($type, $order)
    {
        $arr = [];
        switch ($type) {
            case 'ord_out_dirt':
                $arr = [
                    'VBELN' => $order['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $order['LineNo'],
                    'MATNR' => $order['NewProductCd'],
                    'AdmQnty' => $order['AdmQnty'],
                    'OrdTyep' => $order['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $order['PlaceCD'],
                    'LGORT' => $order['FineFlg'],
                    'PRODUCTCD' => $order['ProductCd'],
                ];
                break;
            case 'adj_out_dirt':
                $arr = [
                    'VBELN' => $order['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $order['LineNo'],
                    'MATNR' => $order['NewProductCd'],
                    'AdmQnty' => $order['AdjustQnty'],
                    'OrdTyep' => $order['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $order['PlaceCD'],
                    'LGORT' => $order['FineFlg'],
                    'PRODUCTCD' => $order['ProductCd'],
                ];
                break;
            case 'move_out_dirt':
                $arr = [
                    'VBELN' => $order['OutStcNo'],
                    'ISDD' => date('Ymd'),
                    'POSNR' => $order['LineNo'],
                    'MATNR' => $order['NewProductCD'],
                    'AdmQnty' => $order['MovAdmQnty'],
                    'OrdTyep' => $order['AUART'],
                    'CHARG' => '00',
                    'WERKS' => $order['MovFrmCD'],
                    'LGORT' => $order['FineFlg'],
                    'PRODUCTCD' => $order['ProductCD'],
                ];
                break;
        }
        return $arr;
    }

}