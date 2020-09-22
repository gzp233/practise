<?php

namespace App\Http\Controllers\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Basic\Attributes;
use App\Models\Storage\Goods;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\Attribute;

class AttributesController extends Controller
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
        if ($request->input('id')) $params[] = ['id', '=', $request->id];
        return sendData(200, '请求成功', Attributes::where($params)->orderBy($sort, 'desc')->paginate($limit)->toArray());
    }
    public function save(Request $request)
    {
        $rule = [
            'state_name' => ['required'],
        ];
        $this->validate($request, $rule);

        $params = $request->post();
        if (isset($params['id'])) {
            $model = Attributes::find($params['id']);
            if (Attributes::where([
                ['state_name', '=', $params['state_name']],
                ['id', '<>', $params['id']],
                ])->count() > 0) {
                return sendData(406, '该属性已存在！');
            }
            DB::transaction(function () use ($model, $params) {
                Goods::where('state_name', $model->state_name)->update(['state_name' => $params['state_name']]);
                $model->state_name = $params['state_name'];
                if (!$model->save($params)) {
                    return sendData(406, '请求失败');
                }
            });
        } else {
            if (Attributes::where('state_name', $params['state_name'])->count() > 0) {
                return sendData(406, '该属性已存在！');
            }
            $model = new Attributes();
            $model->state_name = $params['state_name'];
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
        $attr = Attributes::find($id);
        if (Goods::where('state_name', $attr->state_name)->count() > 0) {
            return sendData(402, '属性无法删除，存在使用该属性的商品！');
        }
        if (!$attr->delete()) {
            return sendData(406, '请求失败');
        }

        return sendData(200, '请求成功');
    }

    /**     
     * * Get Attributes list     
     * *     
     * * @return \Illuminate\Http\JsonResponse     
     * */
    public function getList(Request $request)
    {
        return sendData(200, '', Attributes::all());
    }
}
