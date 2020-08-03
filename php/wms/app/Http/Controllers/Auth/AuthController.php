<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('refresh.token', ['except' => ['login']]);
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
        $credentials['password'] = parseRsa($credentials['password']);
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return sendData(402, '用户名或密码错误!');
        }

        return $this->respondWithToken('bearer ' . $token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();
        $user->roles = [$user->role()->first()->role_name];
        $list = [];
        $permissions = $user->role->permissions->toArray();

        if (count($permissions) > 0) {
            foreach ($permissions as $value) {
                $list[] = $value['permission_code'];
            }
        }
        $user->permissions = $list;
        unset($user->role);

        return sendData(200, '', $user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return sendData(200, '退出成功');
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
        ];

        return sendData(200, '', $data);
    }
}
