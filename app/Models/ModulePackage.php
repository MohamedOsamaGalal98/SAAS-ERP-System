<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulePackage extends Pivot
{
    use SoftDeletes;

    protected static ?string $model = Module::class;
     protected static ?string $recordTitleAttribute = 'label';

     protected $table = 'module_package';
    public $incrementing = true;
    protected $primaryKey = 'id';

    protected $fillable = ['package_id', 'module_id', 'is_active'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getFilamentTitle(): string
    {
        return $this->module?->label ?? 'Module';
    }


}
