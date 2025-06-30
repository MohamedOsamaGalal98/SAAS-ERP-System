<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\PermissionResource\Pages;
use App\Filament\Company\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Authorization';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                   TextInput::make('name')
                    ->required()
                    ->disabled(fn ($record) => in_array($record->name, self::getCorePermissions()))
                    ->unique(ignoreRecord: true),

                Select::make('guard_name')
                    ->label('Guard')
                    ->options([
                        'admin' => 'Admin',
                        'company' => 'Company',
                    ])
                    ->required(),
                    Select::make('company_id')
                    ->label('Company')
                    ->options(fn () => \App\Models\Company::pluck('name', 'id')->toArray()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('name')->searchable(),
                TextColumn::make('guard_name'),
                TextColumn::make('company.name')->label('Company')->default('—'),              
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

   public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
        ];
    }

     public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $companyId = auth('company')->user()->company_id;

        return parent::getEloquentQuery()
            ->where('guard_name', 'company')
            ->where(function ($q) use ($companyId) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', $companyId);
            });
    }
}
