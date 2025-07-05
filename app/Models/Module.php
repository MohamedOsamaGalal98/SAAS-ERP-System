<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'label', 'icon', 'is_active'];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'module_package')
                    ->withPivot(['is_active', 'deleted_at'])
                    ->withTimestamps()
                    ->wherePivotNull('deleted_at');
    }

     public function getFilamentRecordTitleAttribute(): string
    {
        return $this->label;   
    }
}
