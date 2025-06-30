<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class CompanyUser extends Authenticatable
{
    use SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'company_id',
    ];

    protected $table = 'company_users';
    protected $guard_name = 'company';


    // protected static function booted(): void
    // {
    //     static::creating(function ($user) {
    //         if (auth('company')->check()) {
    //             $user->company_id = auth('company')->user()->company_id;
    //         }
    //     });
    // }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

         public function roles()
    {
        return $this->morphToMany(\Spatie\Permission\Models\Role::class, 'model', 'model_has_roles')
            ->where('guard_name', 'company');
    }

    public function permissions()
    {
        return $this->morphToMany(\Spatie\Permission\Models\Permission::class, 'model', 'model_has_permissions') 
            ->where('guard_name', 'company');
    }


}
