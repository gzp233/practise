<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
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
        $data = User::where($params)->orderBy($sort, 'desc')->paginate($limit);

        return sendData(200, '', $data);
    }

    /**
     * 删除用户
     *
     * @param Request $request
     * @return boolean
     */
    public function destroy(Request $request)
    {
        if (!$request->get('id') || !$user = User::find($request->get('id')))  return sendData(402, 'ID不存在！');
        if ($user->is_admin) return sendData(402, '管理员账号无法删除！');
        if (!User::destroy($request->get('id'))) {
            return sendData(402, '删除失败');
        }

        return sendData();
    }

    // 修改密码
    public function modifyPassword(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'password' => 'required|min:6|max:32',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::find($request->id);
        $user->password = bcrypt($request->password);
        $user->save();

        return sendData();
    }
}
