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

interface DirServiceInterface
{
    public function list(array $query);

    public function add(array $params);

    public function edit(array $params);

    public function remove(int $id);

    public function get(int $id);
}
