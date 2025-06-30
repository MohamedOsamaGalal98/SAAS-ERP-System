<?php

namespace App\Filament\Company\Resources\RoleResource\Pages;

use App\Filament\Company\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
   use Illuminate\Auth\Access\AuthorizationException;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;


    public function mount($record): void
    {
        parent::mount($record);

        if ($this->record->company_id === null) {
            throw new AuthorizationException(); // This will trigger 403
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

  
}
