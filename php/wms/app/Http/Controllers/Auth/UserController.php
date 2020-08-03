<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Auth\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
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
            'username' => 'required|min:2|max:20',
            'password' => 'required',
            'role_id' => 'required|numeric',
        ];
    }

    /**
     * 分页获取用户
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $params = [];
        $sort = $request->get('sort') ? $request->get('sort') : 'created_at';
        $limit = $request->get('limit') ? $request->get('limit') : 20;
        if ($request->get('username')) {
            $params[] = ['username', 'like', '%' . $request->get('username') . '%'];
        }
        if ($request->get('role_id')) {
            $params[] = ['role_id', '=', $request->get('role_id')];
        }
        $data = User::where($params)->with('role')->orderBy($sort, 'desc')->paginate($limit);

        return sendData(200, '', $data);
    }

    /**
     * 创建一个用户
     *
     * @param Request $request
     * @return boolean
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->rules);

        $count = User::where("username", $request->get('username'))->count();
        if ($count > 0) {
            return sendData(402, '该用户名已存在');
        }

        $params = $request->all();
        $params['password'] = password_hash($params['password'], PASSWORD_BCRYPT);

        if (User::create($params)) {
            return sendData(200, '创建成功');
        } else {
            return sendData(402, '创建失败');
        }
    }

    public function getById(Request $request)
    {
        return sendData(200, '', User::find($request->get('id')));
    }

    /**
     * 更新用户
     *
     * @param Request $request
     * @return boolean
     */
    public function update(Request $request)
    {
        $this->rules['id'] = 'required|numeric';
        $this->validate($request, $this->rules);

        $params = $request->all();

        $user = User::where("username", $params['username'])->first();
        if ($user && $user->id != $params['id']) {
            return sendData(402, '该用户名已存在');
        }

        $user = User::find($params['id']);
        if (!$user) return sendData(402, 'ID错误');
        if ($user->username == 'admin' && $params['username'] != 'admin') return sendData(402, 'admin账号无法修改用户名');

        $user->username = $params['username'];
        $user->password = password_hash($params['password'], PASSWORD_BCRYPT);
        $user->email = $params['email'];
        $user->phone = $params['phone'];
        $user->role_id = $params['role_id'];

        if ($user->save()) {
            return sendData(200, '更新成功');
        } else {
            return sendData(402, '更新失败');
        }
    }

    /**
     * 删除用户
     *
     * @param Request $request
     * @return boolean
     */
    public function delete(Request $request)
    {
        if (!$request->get('id') || !$role = User::find($request->get('id')))  return sendData(402, 'ID不存在！');
        // 判断是否是超级管理员角色或有用户使用该角色
        if ($role->username == 'admin') return sendData(402, 'admin账号无法删除！');

        if (User::destroy($request->get('id'))) {
            return sendData(200, '删除成功');
        } else {
            return sendData(402, '删除失败');
        }
    }

    /**
     * 获取所有用户
     *
     * @param Request $request
     * @return boolean
     */
    public function getList(Request $request)
    {
        return sendData(200, '', User::all());
    }
}
