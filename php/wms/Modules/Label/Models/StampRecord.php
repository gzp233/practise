<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class StampRecord extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'stamp_records';

    protected $guarded  = [];

}
