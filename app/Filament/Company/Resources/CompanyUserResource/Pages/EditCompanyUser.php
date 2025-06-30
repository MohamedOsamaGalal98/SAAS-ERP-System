<?php

namespace App\Filament\Company\Resources\CompanyUserResource\Pages;

use App\Filament\Company\Resources\CompanyUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyUser extends EditRecord
{
    protected static string $resource = CompanyUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
