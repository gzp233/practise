<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'created_at', 'updated_at'
    ];

    protected $table = 'permission';

    protected $dates = ['delete_at'];

    /**
     * 获取角色名称
     *
     * @return object
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Auth\Role', 'role_permission_related', 'permission_id', 'role_id');
    }

}
