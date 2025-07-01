<?php

namespace App\Filament\Admin\Resources\CompanySubscriptionResource\Pages;

use App\Filament\Admin\Resources\CompanySubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanySubscription extends EditRecord
{
    protected static string $resource = CompanySubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
