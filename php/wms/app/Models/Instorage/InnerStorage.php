<?php

namespace App\Models\Instorage;

use Illuminate\Database\Eloquent\Model;

class InnerStorage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at'
    ];

    protected $table = 'asn';

    public function product()
    {
        return $this->hasOne('App\Models\Base\Product', 'NewPRODUCTCD', 'NewProductCD');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Base\Company', 'CompanyCd', 'CompanyCd');
    }


    public function tag()
    {
        return $this->hasOne('App\Models\Instorage\InnerStorageTag', 'related_id');
    }

}
