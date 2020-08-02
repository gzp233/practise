<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use App\Common\RespCode;
use App\Service\AuthServiceInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\Annotation\Auth;

/**
 * @Controller(prefix="api/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @Inject
     * @var AuthServiceInterface
     */
    protected $authService;

    /**
     * @PostMapping(path="login")
     */
    public function login()
    {
        if ($errMessage = $this->validate([
            'username' => 'required|exists:users',
            'password' => 'required',
        ])) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }

        $credential = $this->request->inputs(['username', 'password']);
        $res = $this->authService->login($credential);
        if ($res['msg']) {
            return $this->error(RespCode::$ERR_LOGIN, $res['msg']);
        }

        return $this->success(['user' => $res['data'], 'token' => $this->authService->getToken($res['data'])]);
    }

    /**
     * @PostMapping(path="register")
     */
    public function register()
    {
        if ($errMessage = $this->validate([
            'username' => 'required|unique:users',
            'password' => 'required',
        ])) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }

        $res = $this->authService->register($this->request->inputs(['username', 'password']));
        if ($res['msg']) {
            return $this->error(RespCode::$ERR_REGISTER, $res['msg']);
        }

        return $this->success(['user' => $res['data'], 'token' => $this->authService->getToken($res['data'])]);
    }

    /**
     * @Auth("jwt")
     * @PostMapping(path="logout")
     */
    public function logout()
    {
        $this->authService->logout();

        return $this->success('退出登录成功');
    }
}
