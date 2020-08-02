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

use App\Service\UserServiceInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Qbhy\HyperfAuth\Annotation\Auth;

/**
 * @Controller(prefix="api")
 * @Auth("jwt")
 */
class UserController extends AbstractController
{
    /**
     * @Inject
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @GetMapping(path="users")
     */
    public function users()
    {
        $query = [['status', '=', 1]];
        $username = $this->request->input('username', '');
        if ($username) {
            $query[] = ['username', 'like',  '%' . $username . '%'];
        }
        $users = $this->userService->list($query);

        return $this->success($users);
    }

    /**
     * @GetMapping(path="users/{id}")
     */
    public function user(int $id)
    {
        $user = $this->userService->getUser($id);

        return $this->success($user);
    }
}
