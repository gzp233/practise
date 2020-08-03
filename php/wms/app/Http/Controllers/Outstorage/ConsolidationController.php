<?php

namespace App\Http\Controllers\Outstorage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\GoodsRecord;
use App\Models\Outstorage\Adjust;
use App\Models\Outstorage\AdjustTag;
use App\Models\Outstorage\MoveOut;
use App\Models\Outstorage\MoveOutTag;
use App\Models\Outstorage\SalesOut;
use App\Models\Outstorage\SalesOutTag;
use App\Models\Base\Unit;
use PhpOffice\PhpWord\TemplateProcessor;
use Excel;
use Milon\Barcode\DNS1D;
use PHPExcel_Worksheet_Drawing;

class ConsolidationController extends OutBaseController
{
    protected $rules = [];

    /**
     * Create a new SalesOutController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 100;
        $where = [];
        if ($request->get('ShopSignNM')) $where[] = ['ShopSignNM', 'like', '%' . $request->get('ShopSignNM') . '%'];
        if ($request->get('vbeln')) $where[] = ['vbeln', 'like', '%' . $request->get('vbeln') . '%'];
        if ($request->get('OutStcNo')) $where[] = ['OutStcNo', 'like', '%' . $request->get('OutStcNo') . '%'];
        if ($request->get('OutStcNo')) $where[] = ['OutStcNo', 'like', '%' . $request->get('OutStcNo') . '%'];
        if ($request->get('FineFlg')) $where[] = ['FineFlg', 'like', '%' . $request->get('FineFlg') . '%'];
        $result = DB::table('ganher')
            ->select('vbeln', 'OutStcNo', 'FineFlg', 'ShopSignNM', 'code',
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('count(NewProductCd) as type'),
                DB::raw('sum(number) as number'))
            ->where($where)->where('status', '未集货')
            ->orderBy('created_at','desc')
            ->groupBy('vbeln', 'OutStcNo', 'FineFlg', 'ShopSignNM', 'code')
            ->get();
        return sendData(200, '', $result);
    }

    public function getByNo(Request $request)
    {
        $outStcNo = $request->all();
        $data = [];
        $a = Adjust::whereIn('AdjustNo', $outStcNo)->with('customer')->sum('AdjustQnty');
        $b = MoveOut::whereIn('MoveNo', $outStcNo)->sum('MovAdmQnty');
        $c = SalesOut::whereIn('OrderNo', $outStcNo)->with('customer')->sum('AdmQnty');
        $adj = Adjust::whereIn('AdjustNo', $outStcNo)->with('customer')->get()->toArray();
        $move = MoveOut::whereIn('MoveNo', $outStcNo)->get()->toArray();
        $sale = SalesOut::whereIn('OrderNo', $outStcNo)->with('customer')->get()->toArray();
        $res = [];
        foreach ($adj as $k => $v) {
            $res[] = $v['customer']['ShopSignNM'];
        }
        foreach ($sale as $k => $v) {
            $res[] = $v['customer']['ShopSignNM'];
        }
        foreach ($move as $key => $value) {
            $res[] = $value['MovToCD'];
        }
        $array = array_flip($res);
        $goods = GoodsRecord::whereIn('odd', $outStcNo)->get();
        $stock = [];
        $product = [];

        foreach ($goods as $key => $val) {
            $stock[] = $val['origin_stock_no'];
            $product[] = $val['product_id'];
        }
        $sto = array_flip($stock);
        $pro = array_flip($product);
        $res = [
            'indent' => count($outStcNo),
            'clientele' => count($array),
            'storage' => count($sto),
            'type' => count($pro),
            'number' => $a + $b + $c,
        ];
        return sendData(200, '', [$res]);
    }

    public function wave(Request $request)
    {
        $OutStcNo = $request->get('id');
        DNS1D::getBarcodePNGPath($OutStcNo, "C39");
        $code = DB::table('ganher')->where('code', $OutStcNo)->pluck('vbeln')->toArray();
        $result_01 = array_flip($code);
        $result_02 = array_flip($result_01);
        $pieces = array_merge($result_02);
        $data[] = ['集货单号：','orderNo'=>$OutStcNo];
        $data[] = [
            '库位号',
            '型号',
            '品名',
            '有效期',
            '防串货',
            'orderNo'=>'SAP单号',
            '箱规',
            '数量',
            '箱数',
            '零头'
        ];
        $records = GoodsRecord::whereIn('odd', $pieces)
            ->orderBy('origin_stock_no', 'asc')
            ->with(['product'])
            ->orderBy('product_id','asc')
            ->get();
        if (!$records) {
            return sendData(402, '没有数据');
        }
        $units = Unit::where('unit_name', "箱")->pluck('number', 'product_id');
        $pre = $prf = [];
        $pre_key = '';
        foreach ($records as $k => $info) {
            $key = $info->product_id . '-' . $info->available_time . '-' .$info->origin_stock_no . '-'.$info->odd;
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
        // 最后一条特殊处理
        $prf[] = $pre[$pre_key];
        $lastRow = null;
        foreach ($prf as  $v) {
            if (isset($units[$v->product_id])) {
                $rule = $units[$v->product_id];
            } else {
                $rule = '0';
            }
            if (!$rule || $rule > $v->number) {
                $box = 0;
                $surplus = $v->number;
            } else {
                $surplus = $v->number % $rule;
                $box = ($v->number - $surplus) / $rule;
            }
            $res = [
                "stock" => $v->origin_stock_no,
                "type" => $v->product->PRODUCTCD,
                "productName" => $v->product->PRODCHINM,
                "Valid" => $v->available_time,
                "code" => $v->is_need_code == '是' ? 'Y' : 'N',
                "orderNo" => $v->odd,
                "rule" => $rule,
                "number" => $v->number,
                "box" => $box,
                "surplus" => $surplus,
            ];
            if ($lastRow && $v->available_time.$v->product_id.$v->origin_stock_no == $lastRow->available_time.$lastRow->product_id.$lastRow->origin_stock_no){
                 $res['stock'] = $res['type'] = $res['productName'] = $res['Valid'] = $res['code'] = '';
            } else {
                $lastRow = $v;
            }
            $v1 = $v->origin_stock_no;
            $arr[$v1][] = $res;
        }
        foreach ($arr as $k) {
            $sum = 0;
            foreach ($k as $value) {
                $sum += $value['number'];
                $data[] = $value;
            }
            $res = [
                "stock" => $k[0]['stock'] . '汇总',
                "type" => '',
                "productName" => '',
                "Valid" => '',
                "code" => '',
                "orderNo" => '',
                "rule" => '',
                "number" => $sum,
                "box" => '',
                "surplus" => '',
            ];
            $data[] = $res;
        }
        $name = date('YmdHis') . rand('1', '999');

        Excel::create($name, function ($excel) use ($data) {
            $excel->sheet('score', function ($sheet) use ($data) {
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path() . '/'.$data[0]['orderNo'].'.png');
                $objDrawing->setHeight(400);
                $objDrawing->setWidth(300);
                $objDrawing->setCoordinates('A2');
                $objDrawing->setOffsetX(100);//写入图片在指定格中的X坐标值
                $objDrawing->setOffsetY(-25);//写入图片在指定格中的Y坐标值
                $objDrawing->setRotation(1);//设置旋转角度
                $objDrawing->getShadow()->setVisible(true);//
                $objDrawing->getShadow()->setDirection(50);//
                $objDrawing->setWorksheet($sheet);
                $sheet->rows($data);
                $styleThinBlackBorderOutlines = array(
                    'borders' => array(
                        'bottom' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THICK, //设置border样式 'color' => array ('argb' => 'FF000000'), //设置border颜色
                        ),
                    ),);
                $list = [];
                foreach ($data as $key => $value) {
                    if ($value['orderNo'] == '') {
                        $list[] = $key;
                    }
                }
                foreach ($list as $k => $v) {
                    $x = $v + 3;
                    $sheet->getStyle('A' . $x . ':' . 'J' . $x)->applyFromArray($styleThinBlackBorderOutlines);
                }

            });
        })->export('xls');
    }

    public function getList(Request $request)
    {
        $all = $request->all();
        try {
            DB::beginTransaction();
            $rand = '17' . substr(date('YmdHis'),3,5) . rand(1000, 9999);
            $update = [
                'status' => '未处理',
                'code' => $rand,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $row = DB::table('ganher')->whereIn('vbeln', $all)->pluck('status');
            foreach ($row as $item) {
                if($item !='未集货'){
                    return sendData(402,'订单已拣货,请刷新页面');
                }
            }
            DB::table('ganher')->whereIn('vbeln', $all)->update($update);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }

    public function show(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $where =[
            '未处理','处理中','已完成','播种完成'
        ];
        $result = DB::table('ganher')
            ->select('code','status',
                DB::raw('MAX(jh_start) as jh_start'),
                DB::raw('MAX(jh_end) as jh_end'),
                DB::raw('MAX(bz_start) as bz_start'),
                DB::raw('MAX(bz_end) as bz_end'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('sum(number) as number'))
            ->whereIn('status',$where)->orderBy('created_at', 'desc')->groupBy('code','status');
        $data = $result->paginate($limit);
        foreach ($data as $key=>$item){
            $row = DB::table('ganher')
                ->select('ShopSignNM','vbeln','NewProductCd')
                ->where('code',$item->code)
                ->groupBy('ShopSignNM','vbeln','NewProductCd')
                ->get();
            $ShopSignNM = array(); //
            foreach ($row as $k => $v) {
                $ShopSignNM[$v->ShopSignNM][] = $v;
            }
            $vbeln = array(); //
            foreach ($row as $k => $v) {
                $vbeln[$v->vbeln][] = $v;
            }
            $NewProductCd = array(); //
            foreach ($row as $k => $v) {
                $NewProductCd[$v->NewProductCd][] = $v;
            }
            $data[$key]->ShopSignNM = count($ShopSignNM);
            $data[$key]->vbeln = count($vbeln);
            $data[$key]->NewProductCd = count($NewProductCd);
            $origin = [];
            foreach ($vbeln as $i =>$r) {
                $origin[] = $i;
            }
            $odd = DB::table('goods_record')->select('origin_stock_no')->whereIn('odd',$origin)->groupBy('origin_stock_no')->get();
            $res = array(); //
            foreach ($odd as $k => $v) {
                $res[$v->origin_stock_no][] = $v;
            }
            $data[$key]->res = count($res);
        }
        return sendData(200, '', $data);
    }

    public function del(Request $request)
    {
        $id = $request->get('id');
        try {
            DB::beginTransaction();
        $update = [
            'status' => '未集货',
            'code' => '',
        ];
         $del = DB::table('ganher')->where('code',$id)->pluck('status')->toArray();
         if(count($del) == 0){
             return sendData(402, '数据获取失败');
         }
         if($del[0] == '未处理'){
             DB::table('ganher')->where('code',$id)->update($update);
         }else{
             return sendData(402, '删除失败');
         }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return sendData(402, $e->getMessage());
        }
        return sendData(200, '成功');
    }
}