<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class StickPick extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'stick_pick';

    protected $guarded  = [];
}
