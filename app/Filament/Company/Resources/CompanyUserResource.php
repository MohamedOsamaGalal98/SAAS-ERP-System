<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\CompanyUserResource\Pages;
use App\Filament\Company\Resources\CompanyUserResource\RelationManagers;
use App\Models\CompanyUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Hidden;

class CompanyUserResource extends Resource
{
    protected static ?string $model = CompanyUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static function beforeCreateUsing($record): void
    {
        $record->company_id = auth('company')->user()?->company_id;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('company_id')
                ->default(fn () => auth('company')->user()?->company_id)
                ->dehydrated(),

                TextInput::make('name')->required(),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                 TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->afterStateHydrated(fn (Component $component, $state) => $component->state(null)),

                Toggle::make('is_active')->label('Active'),

                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->relationship('roles', 'name', function ($query) {
                        $user = auth('company')->user();
                        return $query->where('guard_name', 'company')
                                    ->where(function ($q) use ($user) {
                                        $q->whereNull('company_id')
                                        ->orWhere('company_id', $user->company_id);
                                    });
                    }),

                Select::make('permissions')
                    ->label('Permissions')
                    ->multiple()
                    ->relationship('permissions', 'name', function ($query) {
                        $user = auth('company')->user();
                        return $query->where('guard_name', 'company')
                                    ->where(function ($q) use ($user) {
                                        $q->whereNull('company_id')
                                        ->orWhere('company_id', $user->company_id);
                                    });
                    }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->copyable()->searchable(),
                BooleanColumn::make('is_active')->label('Active'),
                TagsColumn::make('roles.name')->label('Roles')->badge()->sortable(),
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
            'index' => Pages\ListCompanyUsers::route('/'),
            'create' => Pages\CreateCompanyUser::route('/create'),
            'edit' => Pages\EditCompanyUser::route('/{record}/edit'),
        ];
    }

    // public static function canAccess(): bool
    // {
    //     return auth('company')->user()?->can('manage_company_users');
    // }
}
