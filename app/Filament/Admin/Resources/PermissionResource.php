<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;


class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Authorization';
    protected static ?int $navigationSort = 11;

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
           
                TextInput::make('created_at')
                    ->label('Created At')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->toDayDateTimeString() : null),
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
                // TextColumn::make('company.name')->label('Company')->default('—'),    
                 TextColumn::make('company_id')
                    ->label('Company')
                    ->sortable()
                    ->default('—')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '—';
                        }
                        return \App\Models\Company::find($state)?->name ?? '—';
                    }),    
                    TextColumn::make('created_at')->dateTime(),      
                  ])->actions([
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                    ])->bulkActions([
                        Tables\Actions\DeleteBulkAction::make(),
                ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            // 'create' => Pages\CreatePermission::route('/create'),
            // 'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    protected static function getCorePermissions(): array
    {
        return [
            'manage_admins',
            'manage_companies',
            'view_dashboard',
            'manage_contacts',
        ];
    }


      public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->can('manage_admins');
    }
}
