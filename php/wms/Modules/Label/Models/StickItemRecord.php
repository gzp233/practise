<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class StickItemRecord extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'stick_item_records';

    protected $guarded  = [];
}
