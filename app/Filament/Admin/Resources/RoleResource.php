<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Filament\Facades\Filament;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\BadgeColumn;
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
            TextInput::make('name')->required()->unique(ignoreRecord: true),

           Select::make('guard_name')
            ->label('Guard')
            ->options([
                'admin' => 'Admin',
                'company' => 'Company',
            ])
            ->required()
            ->disabled(fn (string $context) => $context === 'edit'),

            Select::make('company_id')
            ->label('Company')
            ->options(fn () => \App\Models\Company::pluck('name', 'id')->toArray())
            ->searchable()
            ->nullable()
            ->disabled(fn (string $context) => $context === 'edit'),


            Select::make('permissions')
                ->label('Permissions')
                ->multiple()
                ->relationship('permissions', 'name')
                ->preload()
                ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('name')->searchable()->sortable(),
                    BadgeColumn::make('guard_name')
                     ->colors([
                    'primary' => 'admin',
                    'success' => 'company',
                        ]),
                    TextColumn::make('company.name')->label('Company')->default('—'),
                    TagsColumn::make('permissions.name')->label('Permissions'),
                        ])->actions([
                            Tables\Actions\EditAction::make(),
                            Tables\Actions\DeleteAction::make(),
                ])->bulkActions([
                    Tables\Actions\DeleteBulkAction::make(),
            ])
            ->filters([
           Filter::make('type')
    ->label('Role Type')
    ->form([
        Select::make('value')
            ->label('Type')
            ->options([
                'system' => 'System-level',
                'company-general' => 'Company-general',
                'company-specific' => 'Company-specific',
            ])
    ])
    ->indicateUsing(function (array $data): ?string {
        return match ($data['value'] ?? null) {
            'system' => 'Type: System-level',
            'company-general' => 'Type: Company-general',
            'company-specific' => 'Type: Company-specific',
            default => null,
        };
    })
    ->query(function ($query, array $data) {
        return match ($data['value'] ?? null) {
            'system' => $query->where('guard_name', 'admin')->whereNull('company_id'),
            'company-general' => $query->where('guard_name', 'company')->whereNull('company_id'),
            'company-specific' => $query->where('guard_name', 'company')->whereNotNull('company_id'),
            default => $query,
        };
    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->can('manage_admins');
    }
}
