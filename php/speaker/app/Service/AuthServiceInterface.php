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

use Qbhy\HyperfAuth\Authenticatable;

interface AuthServiceInterface
{
    public function login(array $credential): array;

    public function logout();

    public function getToken(Authenticatable $user);

    public function register(array $params);
}
