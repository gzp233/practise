<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
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

    protected $table = 'role';

    protected $dates = ['delete_at'];

    /**
     * 获取权限
     *
     * @return object
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Auth\Permission', 'role_permission_related', 'role_id', 'permission_id');
    }

    /**
     * 获取用户
     *
     * @return object
     */
    public function users()
    {
        return $this->hasMany('App\Models\Auth\User');
    }

}
