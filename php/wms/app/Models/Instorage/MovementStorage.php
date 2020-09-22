<?php

namespace App\Models\Instorage;

use Illuminate\Database\Eloquent\Model;

class MovementStorage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    protected $table = 'move_in_dirt';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'NewProductCd');
    }
   
    public function company()
    {
        return $this->hasOne('App\Models\Base\Company', 'CompanyCd', 'CompanyCD');
    }

    public function tag()
    {
        return $this->hasOne('App\Models\Instorage\MovementStorageTag', 'related_id');
    }

}
