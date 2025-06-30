<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;


class AdminUser extends Authenticatable
{
    use SoftDeletes, HasRoles;

    protected $table = 'admin_users';
    protected $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'is_active',
        ];

        public function roles()
    {
        return $this->morphToMany(\Spatie\Permission\Models\Role::class, 'model', 'model_has_roles')
            ->where('guard_name', 'admin');
    }

    public function permissions()
    {
        return $this->morphToMany(\Spatie\Permission\Models\Permission::class, 'model', 'model_has_permissions') 
            ->where('guard_name', 'admin');
    }

}
