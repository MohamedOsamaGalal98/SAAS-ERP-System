<?php

namespace App\Filament\Admin\Resources\PackageModuleResource\Pages;

use App\Filament\Admin\Resources\PackageModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageModule extends EditRecord
{
    protected static string $resource = PackageModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
