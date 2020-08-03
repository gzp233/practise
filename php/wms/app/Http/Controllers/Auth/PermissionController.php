<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Auth\Permission;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    protected $rules = [];
    /**
     * Create a new PermissionsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->rules = [
            'permission_name' => 'required|min:2|max:64',
            'parent_id' => 'required|numeric',
        ];
    }

    /**
     * 分页获取权限列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $params = [];
        if ($request->get('permission_name')) $params[] = ['permission_name', 'like', '%' . $request->get('permission_name') . '%'];
        $data = Permission::where($params)->orderBy($sort, 'desc')->paginate($limit)->toArray();

        return sendData(200, '', $data);
    }

    /**
     * 创建权限
     *
     * @param Request $request
     * @return boolean
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->rules);

        $params = $request->all();
        $count = Permission::where("permission_name", $params['permission_name'])->count();
        if ($count > 0) return sendData(402, '权限名已存在！');
        // 生成唯一权限标识
        $params['permission_code'] = strtoupper(md5(uniqid(md5(microtime(true)), true)));
        if (Permission::create($params)) {
            return sendData(200, '创建成功');
        } else {
            return sendData(402, '创建失败');
        }
    }

    /**
     * Get permission list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        return sendData(200, '', Permission::all());
    }

    public function getById(Request $request)
    {
        return sendData(200, '', Permission::find($request->get('id')));
    }

    /**
     * 更新权限
     *
     * @param Request $request
     * @return boolean
     */
    public function update(Request $request)
    {
        $this->rules['id'] = 'required|numeric';
        $this->validate($request, $this->rules);

        $permission = Permission::where("permission_name", $request->get('permission_name'))->first();
        if ($permission && $permission->id != $request->get('id')) {
            return sendData(402, '该权限名已存在');
        }

        $permission = Permission::find($request->get('id'));

        $permission->permission_name = $request->get('permission_name');
        $permission->desc = $request->get('desc');
        $permission->parent_id = $request->get('parent_id') ? $request->get('parent_id') : 0;

        if ($permission->save()) {
            return sendData(200, '更新成功');
        } else {
            return sendData(402, '更新失败');
        }
    }

    /**
     * 获取权限树状结构
     *
     * @param Request $request
     * @return JSON
     */
    public function getTree(Request $request)
    {
        $permissions = Permission::all()->toArray();
        $result = $this->getTreeData($permissions);

        return sendData(200, '', $result['children']);
    }

    /**
     * 递归组织数据
     *
     * @param array $data
     * @param integer $parent_id
     * @param array $result
     * @return array $result
     */
    private function getTreeData($data, $parent_id = 0, $result = [])
    {
        $count = 0;
        foreach ($data as $key => $value) {
            if ($value['parent_id'] == $parent_id) {
                $count += 1;
                $result['children'][] = $value;
                unset($data[$key]);
            }
        }
        if ($count !== 0) {
            foreach ($result['children'] as $index => $item) {
                $result['children'][$index] = $this->getTreeData($data, $item['id'], $item);
            }
        }

        return $result;
    }

    /**
     * 删除权限
     *
     * @param Request $request
     * @return boolean
     */
    public function delete(Request $request)
    {
        if (!$request->get('id') || !$permission = Permission::find($request->get('id')))  return sendData(402, 'ID不存在！');
        // 判断是否有超级管理员之外的角色使用该权限，有则提示无法删除
        if ($permission->roles) {
            $roles = $permission->roles->toArray();
            if (count($roles) > 1 || (count($roles) === 1 && $roles[0]['role_name'] != 'admin')) {
                $role_name = $roles[0]['role_name'] == 'admin' ? $roles[1]['role_name'] : $roles[0]['role_name'];
                return sendData(402, '请先删除角色:【' . $role_name . '】再删除该权限！');
            }
        }
        // 判断是否有子权限
        if (Permission::where('parent_id', $request->get('id'))->count() > 0) {
            return sendData(402, '请先删除子权限');
        }
        if (Permission::destroy($request->get('id'))) {
            return sendData(200, '删除成功');
        } else {
            return sendData(402, '删除失败');
        }
    }
}
