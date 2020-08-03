<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'items';

    protected $guarded  = [];

}
