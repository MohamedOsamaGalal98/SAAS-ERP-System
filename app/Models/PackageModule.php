<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageModule extends Model
{
    use SoftDeletes;

        protected $fillable = [
            "package_id",
            "module_name",
            "is_active"
        ];

        
        public function package()
        {
            return $this->belongsTo(Package::class);
        }

}
