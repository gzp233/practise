<?php

namespace App\Http\Controllers\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Basic\Area;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit') ? $request->limit : 20;
        $sort = $request->input('sort') ? $request->sort : 'created_at';
        $params = [];
        if ($request->input('area_name')) $params[] = ['area_name', '=', $request->area_name];
        if ($request->input('warehouse_id')) $params[] = ['warehouse_id', '=', $request->warehouse_id];
        $result = Area::where($params)->orderBy($sort, 'desc')->with('warehourse')->paginate($limit)->toArray();

        return sendData(200, '请求成功', $result);
    }
    public function save(Request $request)
    {
        $rule = [
            'area_name' => ['required'],
            'warehouse_id' => ['required', 'integer'],
        ];
        $this->validate($request, $rule);
        $params = $request->post();
        if (isset($request->id)) {
            Area::where('id', $request->id)->update($params);
        } else {
            Area::create($params);
        }

        return sendData(200, '请求成功');
    }

    public function getById(Request $request) 
    {
        return sendData(200, '', Area::findOrFail($request->get('id')));
    }

    public function del(Request $request)
    {
        $rule = [
            'id' => ['required', 'integer'],
        ];
        $this->validate($request, $rule);
        $id = $request->post('id');
        $area = Area::with('locations')->find($id);
        if ($area && count($area->locations)> 0) {
            return sendData(406, '存在库位，无法删除');
        }
        if (!Area::destroy($id)) {
            return sendData(406, '请求失败');
        }

        return sendData(200, '请求成功');
    }

    public function getAll()
    {
        return sendData(200, '', Area::all());
    }
}
