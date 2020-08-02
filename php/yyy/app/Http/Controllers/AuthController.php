<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('username', 'password');
        if (!$data = $this->respondWithToken($credentials)) {
            return sendData(402, '用户名或密码错误!');
        }
        $loginIp = $request->getClientIp();
        $user = auth()->user();
        if ($loginIp != $user->lastLoginIp) User::where('id', $user->id)->update(['lastLoginIp' => $loginIp]);

        return sendData(200, '', $data);
    }

    // 注册用户
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|min:2|max:32|unique:users',
            'password' => 'required|min:6|max:32',
            'confirm_password' => 'required|same:password',
            'email' => 'required|email',
        ]);

        $data = [
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'is_admin' => 0,
            'avatar' => '',
            'lastLoginIp' => $request->getClientIp()
        ];

        if (!User::create($data)) {
            return sendData(402, '创建失败');
        }
        $data = $this->respondWithToken($request->only('username', 'password'));

        return sendData(200, '', $data);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return sendData(200, '', auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return sendData(200, '退出成功');
    }

    /**
     * Get the token array structure.
     *
     * @param  array $credentials
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($credentials)
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return false;
        }
        $data = [
            'access_token' => 'Bearer ' . $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];

        return $data;
    }
}
