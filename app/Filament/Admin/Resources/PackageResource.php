<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ModulesResource\RelationManagers\ModulesRelationManager as RelationManagersModulesRelationManager;
use App\Filament\Admin\Resources\PackageResource\Pages;
use App\Filament\Admin\Resources\PackageResource\RelationManagers;
use App\Filament\Admin\Resources\PackageResource\RelationManagers\ModulesRelationManager;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;


class PackageResource extends Resource
{

    protected static ?string $model = Package::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Subscriptions';
    protected static ?int    $navigationSort  = 10;


    /* ---------- Form ---------- */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('monthly_price')->numeric()->suffix('EGY')->required(),
                TextInput::make('annual_price')->numeric()->suffix('EGY')->required(),
                TextInput::make('file_storage')->numeric()->suffix('MB')->required(),
                TextInput::make('max_employees')->numeric()->required(),
                Toggle::make('is_active')->label('Active')->default(true),

            ]);
    }


    /* ---------- Table ---------- */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('monthly_price')->money('EGY'),
                TextColumn::make('annual_price')->money('EGY'),
                TextColumn::make('file_storage')->suffix(' MB'),
                TextColumn::make('max_employees'),
                BooleanColumn::make('is_active'),     
                TagsColumn::make('modules.label')
                ->label('Modules')
                ->limit(3)
                ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    /* ---------- Relations ---------- */
    public static function getRelations(): array
    {
        return [
            ModulesRelationManager::class,
        ];
    }

    
    /* ---------- Pages ---------- */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
