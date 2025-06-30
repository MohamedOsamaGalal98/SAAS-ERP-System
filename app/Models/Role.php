<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'company_id',
    ];

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
    return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }   
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
