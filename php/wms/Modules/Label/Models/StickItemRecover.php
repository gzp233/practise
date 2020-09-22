<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class StickItemRecover extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'stick_item_recover';

    protected $guarded  = [];
}
