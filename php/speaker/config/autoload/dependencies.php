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
return [
    // services
    App\Service\UserServiceInterface::class => App\Service\UserService::class,
    App\Service\AuthServiceInterface::class => App\Service\AuthService::class,
    App\Service\DirServiceInterface::class => App\Service\DirService::class,
    App\Service\TipServiceInterface::class => App\Service\TipService::class,

    // util
    Hyperf\Contract\LengthAwarePaginatorInterface::class => App\Util\LengthAwarePaginator::class,
];
