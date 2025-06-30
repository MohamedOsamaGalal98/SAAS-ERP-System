<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'guard_name',
        'company_id',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions')
            ->where('guard_name', 'admin');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
