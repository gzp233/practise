<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class LabelStock extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'label_stock';

    protected $guarded  = [];

}
