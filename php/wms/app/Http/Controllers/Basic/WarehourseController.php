<?php

namespace App\Http\Controllers\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Basic\Warehourse;
use App\Models\Basic\Area;

class WarehourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('refresh.token');
    }

    public function index(Request $request) {
        $limit = $request->input('limit') ? $request->limit : 20;
        $sort = $request->input('sort') ? $request->sort : 'created_at';
        $params = [];
        if ($request->input('id')) $params[] = ['id', '=', $request->id];
        return sendData(200, '请求成功', Warehourse::where($params)->orderBy($sort, 'desc')->paginate($limit)->toArray());
    }
    public function save(Request $request) {
        $rule = [
            'warehouse_name' => ['required'],
            'desc' => ['string'],
        ];
        $this->validate($request, $rule);
        $params = $request->post();
        if (isset($params['id'])) {
            $model = Warehourse::find($params['id']);
        } else {
            $model = new Warehourse();
        }
        $model->warehouse_name = $params['warehouse_name'];
        $model->desc = $params['desc'];
        if (!$model->save($params)) {
            return sendData(406, '请求失败');
        }

        return sendData(200, '请求成功');
    }

    public function del(Request $request) {
        $rule = [
            'id' => ['required', 'integer'],
        ];
        $this->validate($request, $rule);
        $id = $request->post('id');
        // 先查询当前有没有库区使用这个仓库
        $area = Area::where('warehouse_id', $id)->get()->toArray();
        
        if (!empty($area)) {
            return sendData(410, '修改当前仓库下属的库区');
        }
        if (!Warehourse::destroy($id)) {
            return sendData(406, '请求失败');
        }

        return sendData(200, '请求成功');
    }

    /**
     * Get Warehourse list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        $whs = Warehourse::with('areas')->get();
        $data = [];
        foreach ($whs as $key => $wh) {
            if (count($wh->areas) != 0) $data[] = $wh;
        }
        return sendData(200, '', $data);
    }

    /**
     * Get Warehourse list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        return sendData(200, '',  Warehourse::all());
    }
}
