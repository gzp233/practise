<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class StickLabelRecord extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'stick_label_records';

    protected $guarded  = [];
}
