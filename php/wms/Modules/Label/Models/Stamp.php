<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stamp extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'stamps';

    protected $guarded  = [];

    public function user()
    {
        $instance = new User;
        $instance->setConnection('mysql');
        $query = $instance->newQuery();
        return new BelongsTo($query, $this, 'user_id', 'id', null);
    }
}
