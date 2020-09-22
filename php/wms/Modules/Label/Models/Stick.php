<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stick extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'sticks';

    protected $guarded  = [];

    public function picks()
    {
        return $this->hasMany('Modules\Label\Models\StickPick', 'id', 'stick_id');
    }

    public function user()
    {
        $instance = new User;
        $instance->setConnection('mysql');
        $query = $instance->newQuery();
        return new BelongsTo($query, $this, 'user_id', 'id', null);
    }
}
