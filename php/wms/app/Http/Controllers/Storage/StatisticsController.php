<?php

namespace App\Http\Controllers\Storage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Storage\Appear;
use Illuminate\Support\Facades\DB;
use App\Models\Storage\Enter;
use Excel;

class StatisticsController extends Controller
{
    protected $rules = [];

    /**
     * Create a new GoodsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function enter(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $params = [];
        if ($request->get('odd')) $params[] = ['odd',  'like', '%' . $request->get('odd')];
        if ($request->get('PRODUCTCD')) $params[] = ['PRODUCTCD',  'like', '%' . $request->get('PRODUCTCD')];
        if ($request->get('state_name')) $params[] = ['state_name',  'like', '%' . $request->get('state_name')];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['created_at', '>=', $starttime];
            $params[] = ['created_at', '<=', $endtime];
        }
        $result = DB::table('in_gather')->where($params)->orderBy($sort, 'desc')->paginate($limit);
        return sendData(200, '请求成功', $result);
    }

    public function appear(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $params = [];
        if ($request->get('VBELN')) $params[] = ['VBELN',  'like', '%' . $request->get('VBELN')];
        if ($request->get('PRODUCTCD')) $params[] = ['PRODUCTCD',  'like', '%' . $request->get('PRODUCTCD')];
        if ($request->get('created_at') && $request->get('created_at')[0] && $request->get('created_at')[0] != 'null') {
            $starttime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[0]));
            $endtime = date('Y-m-d H:i:s', strtotime($request->get('created_at')[1]));
            $params[] = ['created_at', '>=', $starttime];
            $params[] = ['created_at', '<=', $endtime];
        }
        $result = Appear::where($params)->with(['product'])->orderBy($sort, 'desc')->paginate($limit);
        //        $outStc = DB::table('ord_out_ensure')->select('VBELN')->get();
        //            foreach ($result as $k => $item) {
        //                $data[] = [
        //                    'VBELN' => $item->VBELN,
        //                ];
        //            }
        //        foreach ($data as $v){
        //            $v=join(',',$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        //            $temp[]=$v;
        //        }
        //        $temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
        //        $ord = DB::table('ord_out_dirt as o')
        //                ->select('o.OrderNo','o.OutStcNo','c.ShopSignNM','o.AdmQnty','o.PRODUCTCD','p.PRODCHINM')
        //                ->leftJoin('customer as c','c.CUSTOMERCD','=','o.CustomerCd')
        //                ->leftJoin('product as p','p.PRODUCTCD','=','o.PRODUCTCD')
        //                ->whereIn('OutStcNo',$temp)
        ////                ->tosql();
        ////        dd($ord);
        //                ->get();
        //        dd($ord);
        //        $adj = DB::table('adj_out_dirt')->whereIn('OutStcNo',$temp)->get();
        //        $data = [];
        //        foreach ($ord as $item => $key) {
        //            dd($key);
        //            $data = [
        //              'OrderNo'=>$key->OrderNo,
        //              'OutStcNo'=>$key->OutStcNo,
        //              'ShopSignNM'=>$key->ShopSignNM,
        //              'OrderNo'=>$key->OrderNo,
        //            ];
        //        }
        //        dd($data);

        return sendData(200, '请求成功', $result);
    }


    public function export(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            $all = $request->all();
            $where = (array) json_decode($all['query']);
            foreach ($where as $key => $val) {
                if ($val == '') {
                    unset($where[$key]);
                }
                if ($key == 'created_at') {
                    $starttime = date('Y-m-d H:i:s', strtotime($where['created_at'][0]));
                    $endtime = date('Y-m-d H:i:s', strtotime($where['created_at'][1]));
                    $where[] = ['created_at', '>=', $starttime];
                    $where[] = ['created_at', '<=', $endtime];
                }
            }
            unset($where['created_at']);
            $arr = DB::table('in_gather')->where($where)->orderBy('created_at', 'desc')->get();
        }else{
            $pieces = explode(",", $id);
            $arr = DB::table('in_gather')->whereIn('id', $pieces)->orderBy('created_at', 'desc')->get();
        }
        $res = [['品名', '产品代码', '发票号', '数量', '库位号', '创建时间']];
        foreach ($arr as $key) {
            $res[] = [
                $key->PRODCHINM,
                $key->PRODUCTCD,
                $key->odd,
                $key->number,
                $key->state_name,
                $key->created_at,
            ];
        }
        $name = '入库汇总';
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),
                    ),
                );
                $sheet->rows($res);
                $count = count($res);
                $sheet->getStyle('A1:' . 'F' . $count)->applyFromArray($styleThinBlackBorderOutline);
            });
        })->export('xls');
    }


    public function exports(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            $all = $request->all();
            $where = (array) json_decode($all['query']);
            foreach ($where as $key => $val) {
                if ($val == '') {
                    unset($where[$key]);
                }
                if ($key == 'created_at') {
                    $starttime = date('Y-m-d H:i:s', strtotime($where['created_at'][0]));
                    $endtime = date('Y-m-d H:i:s', strtotime($where['created_at'][1]));
                    $where[] = ['created_at', '>=', $starttime];
                    $where[] = ['created_at', '<=', $endtime];
                }
            }
            unset($where['created_at']);
            $arr = Appear::where($where)->with(['product'])->orderBy('created_at', 'desc')->get();
        }else{
            $pieces = explode(",", $id);
            $arr = Appear::whereIn('id', $pieces)->with(['product'])->orderBy('created_at', 'desc')->get();
        }
        $res = [['品名', '产品代码', '发票号', '数量', '创建时间']];
        foreach ($arr as $key) {
            $res[] = [
                $key['product']['PRODCHINM'],
                $key['product']['PRODUCTCD'],
                $key['VBELN'],
                $key['AdmQnty'],
                $key['created_at'],
            ];
        }
        $name = '出库汇总';
        Excel::create($name, function ($excel) use ($res) {
            $excel->sheet('score', function ($sheet) use ($res) {
                $styleThinBlackBorderOutline = array(
                    'borders' => array(
                        'allborders' => array( //设置全部边框
                            'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                        ),
                    ),
                );
                $sheet->rows($res);
                $count = count($res);
                $sheet->getStyle('A1:' . 'E' . $count)->applyFromArray($styleThinBlackBorderOutline);
            });
        })->export('xls');
    }
}
