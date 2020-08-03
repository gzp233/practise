<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Auth\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    protected $rules = [];
    /**
     * Create a new RoleController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token');
        $this->rules = [
            'role_name' => 'required|min:2|max:64',
            'desc' => 'min:2|max:128'
        ];
    }

    /**
     * 分页获取角色
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $params = [];
        if ($request->get('role_name')) $params[] = ['role_name', 'like', '%'.$request->get('role_name').'%'];
        $data = Role::where($params)->orderBy($sort, 'desc')->paginate($limit)->toArray();

        return sendData(200, '', $data);
    }

    /**
     * Get role list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        return sendData(200, '', Role::all());
    }

    public function getById(Request $request) {
        return sendData(200, '', Role::find($request->get('id')));
    }

    /**
     * 创建一个角色
     *
     * @param Request $request
     * @return boolean
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->rules);

        $count = Role::where("role_name", $request->get('role_name'))->count();
        if($count > 0) {
            return sendData(402, '该角色名已存在');
        }
        
        if (Role::create($request->all())) {
            return sendData(200, '创建成功');
        } else {
            return sendData(402, '创建失败');
        }
    }

    /**
     * 更新角色信息
     *
     * @param Request $request
     * @return boolean
     */
    public function update(Request $request)
    {
        $this->rules['id'] = 'required|numeric';
        $this->validate($request, $this->rules);
        if($request->get('role_name') == 'admin'){
            return sendData(402, '超级管理员不允许修改');
        }
        $role = Role::where("role_name", $request->get('role_name'))->first();
        if($role && $role->id != $request->get('id')) {
            return sendData(402, '该角色名已存在');
        }

        $role = Role::find($request->get('id'));
        if (!$role) return sendData(402, 'ID错误');

        $role->role_name = $request->get('role_name');
        $role->desc = $request->get('desc');

        if ($role->save()){
            return sendData(200, '更新成功');
        } else {
            return sendData(402, '更新失败');
        }
    }

    /**
     * 删除角色
     *
     * @param Request $request
     * @return boolean
     */
    public function delete(Request $request)
    {
        if (!$request->get('id') || !$role = Role::find($request->get('id')))  return sendData(402, 'ID不存在！');
        // 判断是否是超级管理员角色或有用户使用该角色
        if ($role->role_name == 'admin') return sendData(402, '超级管理员无法删除！');
        if (count($role->users) > 0)  return sendData(402, '请先删除角色下的用户,再删除该角色！');

        if (Role::destroy($request->get('id'))){
            return sendData(200, '删除成功');
        } else {
            return sendData(402, '删除失败');
        }
    }

    /**
     * 修改角色的权限
     *
     * @param Request $request
     * @return boolean
     */
    public function changePermission(Request $request)
    {
        if (!$request->get('id') || !$role = Role::find($request->get('id'))) return sendData(402, 'ID不存在！');
        $permission_ids = $request->get('permissions');
        if($role->permissions()->sync($permission_ids)) {
            return sendData(200, '修改成功');
        } else {
            return sendData(402, '修改失败');
        }
    }

    /**
     * 根据role_id获取所有权限
     *
     * @param Request $request
     * @return void
     */
    public function getPermissions(Request $request)
    {
        if (!$request->get('id') || !$role = Role::find($request->get('id'))) return sendData(402, 'ID不存在！');
        $permissions = [];

        foreach ($role->permissions->toArray() as $permission) {
            $permissions[] = $permission['id'];
        }
        unset($role->permissions);
        $role->permissions = $permissions;
        
        return sendData(200, '', $role);
    }
}
