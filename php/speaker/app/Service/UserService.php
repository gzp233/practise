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
namespace App\Service;

use App\Model\User;

class UserService implements UserServiceInterface
{
    /**
     * 用户列表.
     * @return mixed
     */
    public function list(array $query)
    {
        return User::where($query)->paginate();
    }

    /**
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model
     */
    public function getUser(int $id)
    {
        return User::query()->find($id);
    }
}
