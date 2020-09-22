<?php

namespace Modules\Label\Models;

use Illuminate\Database\Eloquent\Model;

class StickLabelRecover extends Model
{
    protected $connection = 'labelDB';

    protected $table = 'stick_label_recover';

    protected $guarded  = [];
}
