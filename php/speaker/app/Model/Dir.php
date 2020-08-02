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
namespace App\Model;

use Hyperf\Database\Model\Concerns\CamelCase;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $dirName
 * @property int $pid
 * @property string $icon
 * @property string $desc
 * @property int $status
 * @property \Carbon\Carbon $createdAt
 * @property \Carbon\Carbon $updatedAt
 */
class Dir extends Model
{
    use CamelCase;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dirs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['dir_name', 'icon', 'pid', 'desc'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'pid' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function tips()
    {
        return $this->hasMany(Tip::class, 'dir_id', 'id');
    }
}
