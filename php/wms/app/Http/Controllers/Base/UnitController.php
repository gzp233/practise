<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Base\Unit;
use App\Models\Base\Product;
use Illuminate\Support\Facades\Redis;

class UnitController extends Controller
{
    private $rule = [
        'unit_name' => ['required'],
        'product_id' => ['required'],
        'number' => ['required', 'integer'],
    ];

    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function create(Request $request)
    {
        $this->validate($request, $this->rule);
        $flag = Unit::where($request->only('unit_name', 'product_id'))->first();
        if($flag){
            return sendData(406, '该单位已存在');
        }
        if (!Unit::create($request->only('unit_name', 'product_id', 'number'))) {
            return sendData(406, '请求失败');
        }
        $product = Product::with('units')->find($request->product_id);
        Redis::set($product->PRODUCTCD, json_encode($product->toArray()));
        return sendData(200, '请求成功');
    }

    public function update(Request $request)
    {
        $this->validate($request, $this->rule);
        $params = $request->all();
        if (empty($params['id'])) return sendData(406, 'ID为空');

        $where = [
            'product_id'=>$params['product_id'],
            'unit_name'=>$params['unit_name'],
        ];
        $flag = Unit::where('id',$params['id'])->first();
        if($flag->unit_name != $params['unit_name']){
            //查询单位是否存在
            $res = Unit::where($where)->first();
            if($res){
                return sendData(406, '该单位已存在');
            }
        }
        $unit = Unit::find($params['id']);
        $unit->unit_name = $params['unit_name'];
        $unit->number = $params['number'];
        $unit->product_id = $params['product_id'];
        
        if (!$unit->save()) {
            return sendData(406, '请求失败');
        }

        $product = Product::with('units')->find($unit->product_id);

        Redis::set($product->PRODUCTCD, json_encode($product->toArray()));
        return sendData(200, '请求成功');
    }

    public function del(Request $request)
    {
        $rule = [
            'id' => ['required', 'integer'],
        ];
        $this->validate($request, $rule);
        $id = $request->post('id');
        $unit = Unit::find($id);
        $product = Product::with('units')->find($unit->product_id);
        Redis::del($product->PRODUCTCD);
        if (!Unit::destroy($id)) {
            return sendData(406, '请求失败');
        }
        return sendData(200, '请求成功');
    }

}
