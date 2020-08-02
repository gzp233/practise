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
namespace App\Util;

use Hyperf\Paginator\LengthAwarePaginator as HyperfLengthAwarePaginator;

class LengthAwarePaginator extends HyperfLengthAwarePaginator
{
    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'current_page' => $this->currentPage(),
            'data' => $this->items->toArray(),
            'per_page' => $this->perPage(),
            'total' => $this->total(),
        ];
    }
}
