<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource\RelationManagers;
use App\Models\AdminUser;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Tables\Filters\TernaryFilter;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';
    protected static ?string $label = 'User';
    protected static ?string $pluralLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->afterStateHydrated(fn (Component $component, $state) => $component->state(null)),
                Toggle::make('is_super_admin')->label('Super Admin'),
                Toggle::make('is_active')->label('Active'),

                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->relationship('roles', 'name', fn ($query) => $query->where('guard_name', 'admin')),

                Select::make('permissions')
                    ->label('Permissions')
                    ->multiple()
                    ->relationship('permissions', 'name', fn ($query) => $query->where('guard_name', 'admin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                BooleanColumn::make('is_super_admin')->label('Super Admin'),
                BooleanColumn::make('is_active')->label('Active'),
                TagsColumn::make('roles.name')->label('Roles'),
                TagsColumn::make('permissions.name')->label('Permissions'),
            ])
            ->filters([
                 TernaryFilter::make('is_active')
                ->label('Active status')
                ->placeholder('All')
                ->trueLabel('Active')
                ->falseLabel('Inactive'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->can('manage_admins');
    }
}
