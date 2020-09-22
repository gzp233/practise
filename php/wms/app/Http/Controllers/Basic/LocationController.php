<?php

namespace App\Http\Controllers\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Basic\Location;
use App\Models\Storage\Goods;
use Illuminate\Support\Facades\DB;


class LocationController extends Controller
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
        if ($request->input('stock_no')) $params[] = ['stock_no', '=', $request->stock_no];
        if ($request->input('area_id')) $params[] = ['area_id', '=', $request->area_id];
        $result = Location::where($params)
            ->whereHas('area', function ($q) use ($request) {
                if ($request->input('area_id')) $q->where('area_id', $request->input('area_id'));
            })
            ->orderBy($sort, 'desc')
            ->with('area','state')
            ->paginate($limit);

        return sendData(200, '请求成功', $result);
    }

    public function getById(Request $request)
    {
        return sendData(200, '', Location::with('area','state')->findOrFail($request->get('id')));
    }

    public function getLocationsByAreaIds(Request $request)
    {
        if (!$request->input('area_ids')) return sendData(402, '获取库位失败');
        $status = $request->input('status');
        $stock_no = $request->input('stock_no');
        $nos = DB::table('goods')->pluck('stock_no')->all();
        $nos = array_unique($nos);
        $q = Location::whereIn('area_id', $request->input('area_ids'))->where([['stock_no', 'like', '%' . $stock_no . '%']])->limit(20);
        if ($status == 1) {
            $data = $q->whereNotIn('stock_no', $nos)->get();
        } elseif ($status == 2) {
            $data = $q->whereIn('stock_no', $nos)->get();
        } else {
            $data = $q->get();
        }

        return sendData(200, '', $data);
    }

    public function getLocationsAll(Request $request)
    {
        $data = Location::get();
        return sendData(200, '', $data);
    }

    public function save(Request $request)
    {
        $rule = [
            'stock_no' => ['required'],
            'area_id' => ['required', 'integer'],
        ];
        $this->validate($request, $rule);
        $params = $request->all();
        if (isset($params['id'])) {
            $model = Location::find($params['id']);
            if (Location::where([
                ['stock_no', '=', $params['stock_no']],
                ['id', '<>', $params['id']],
                ])->count() > 0) {
                return sendData(406, '该库位已存在！');
            }
            DB::transaction(function () use ($model, $params) {
                Goods::where('stock_no', $model->stock_no)->update(['stock_no' => $params['stock_no']]);
                $model->stock_no = $params['stock_no'];
                $model->stock_state_id = $params['stock_state_id'];
                if (!$model->save($params)) {
                    return sendData(406, '请求失败');
                }
            });
        } else {
            if (Location::where('stock_no', $params['stock_no'])->count() > 0) {
                return sendData(406, '该库位号已存在！');
            }
            $model = new Location();
            $model->stock_no = $params['stock_no'];
            $model->area_id = $params['area_id'];
            $model->stock_state_id = $params['stock_state_id'];
            if (!$model->save($params)) {
                return sendData(406, '请求失败');
            }
        }

        return sendData(200, '请求成功');
    }

    public function del(Request $request)
    {
        $rule = [
            'id' => ['required', 'integer'],
        ];
        $this->validate($request, $rule);
        $id = $request->post('id');
        $location = Location::find($id);
        if (!$location) return sendData(406, '未找到该库位');
        if (Goods::where('stock_no', $location->stock_no)->count() > 0) {
            return sendData(406, '该库位存在商品，无法删除');
        }

        if (!$location->delete()) {
            return sendData(406, '删除失败');
        }

        return sendData(200, '请求成功');
    }

    public function stockOut(Request $request)
    {
        $data = $request->all();
            $id = [
                'id' => $data['id']
            ];
            $res = Location::where($id)->first()->toArray();
            if ($res['status'] == '0') {
                DB::table('stock')->where('id', $res['id'])->update(['status' => '1']);
            } else {
                DB::table('stock')->where('id', $res['id'])->update(['status' => '0']);
            }
        return sendData(200,'成功',$res);
    }
}
