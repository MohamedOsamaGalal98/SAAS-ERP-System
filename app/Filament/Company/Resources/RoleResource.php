<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\RoleResource\Pages;
use App\Filament\Company\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Filters\Filter;

class RoleResource extends Resource
{
     protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Authorization';
    protected static ?int $navigationSort = 10;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('guard_name')
                ->label('Guard')
                ->default('company')
                ->dehydrated(),

                Hidden::make('company_id')
                    ->default(fn () => auth('company')->user()->company_id)
                    ->dehydrated(),

                TextInput::make('name')
                    ->label('Role Name')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name', fn ($query) => $query
                        ->where('guard_name', 'company')
                        ->where(function ($q) {
                            $companyId = auth('company')->user()->company_id;
                            $q->whereNull('company_id')->orWhere('company_id', $companyId);
                    })),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('company.name')
                ->label('Company')
                ->sortable()
                ->default('—')
                ->formatStateUsing(fn ($state) => $state ?? '—'),
                TagsColumn::make('permissions.name')->label('Permissions'),
            ])
            ->filters([
            Filter::make('type')
                ->label('Role Type')
                ->form([
                    Select::make('value')
                        ->label('Type')
                        ->options([
                            'company-general' => 'Company-general',
                            'company-specific' => 'Company-specific',
                    ])
                ])
                ->indicateUsing(function (array $data): ?string {
                    return match ($data['value'] ?? null) {
                        'company-general' => 'Type: Company-general',
                        'company-specific' => 'Type: Company-specific',
                        default => null,
                    };
                })
                ->query(function ($query, array $data) {
                    return match ($data['value'] ?? null) {
                        'company-general' => $query->where('guard_name', 'company')->whereNull('company_id'),
                        'company-specific' => $query->where('guard_name', 'company')->whereNotNull('company_id'),
                        default => $query,
                    };
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->company_id !== null),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->company_id !== null),


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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        $companyId = auth('company')->user()->company_id;

        return parent::getEloquentQuery()
            ->where('guard_name', 'company')
            ->where(function ($q) use ($companyId) {
                $q->whereNull('company_id')->orWhere('company_id', $companyId);
            });
    }


}
