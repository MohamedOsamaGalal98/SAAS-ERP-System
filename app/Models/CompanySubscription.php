<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySubscription extends Model
{
     use SoftDeletes;

    protected $fillable = [
        "company_id",
        "package_id",
        "subscribed_at",
        "expires_at",
        "unsubscribed_at",
        "is_active",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}
