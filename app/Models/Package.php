<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
     use SoftDeletes;

    protected $fillable = [
        "name",
        "monthly_price",
        "annual_price",
        "file_storage",
        "max_employees",
        "is_active"
    ];


        public function modules()
    {
        return $this->hasMany(PackageModule::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(CompanySubscription::class);
    }

}
