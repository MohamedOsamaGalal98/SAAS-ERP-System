<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanySubscriptionResource\Pages;
use App\Filament\Admin\Resources\CompanySubscriptionResource\RelationManagers;
use App\Models\CompanySubscription;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanySubscriptionResource extends Resource
{
    protected static ?string $model = CompanySubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Subscriptions';
    protected static ?int    $navigationSort  = 12;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('company_id')->relationship('company', 'name')->required(),
                Select::make('package_id')->relationship('package', 'name')->required(),
                DatePicker::make('subscribed_at')->required(),
                DatePicker::make('expires_at')->required(),
                DatePicker::make('unsubscribed_at')
                    ->label('Unsubscribed At')
                    ->nullable()
                    ->default(null),
                Toggle::make('is_active')->label('Active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Company')->searchable(),
                TextColumn::make('package.name')->label('Package'),
                TextColumn::make('subscribed_at')->date(),
                TextColumn::make('expires_at')->date(),
                TextColumn::make('unsubscribed_at')->date(),
                BooleanColumn::make('is_active'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanySubscriptions::route('/'),
            'create' => Pages\CreateCompanySubscription::route('/create'),
            'edit' => Pages\EditCompanySubscription::route('/{record}/edit'),
        ];
    }
}
