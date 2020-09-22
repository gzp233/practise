<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemMove extends Model
{

    protected $connection = 'labelDB';

    protected $table = 'item_move';

    protected $guarded  = [];

    public function user()
    {
        $instance = new User;
        $instance->setConnection('mysql');
        $query = $instance->newQuery();
        return new BelongsTo($query, $this, 'user_id', 'id', null);
    }
}
