<?php

namespace App\Filament\Admin\Resources\PackageResource\RelationManagers;

use App\Models\Module;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{Select, Toggle};
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\{BooleanColumn, TextColumn};
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;

            
class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';  

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('module_id')
            ->label('Module')
            ->options(fn () => Module::orderBy('label')->pluck('label', 'id')->toArray())
            ->searchable()
            ->placeholder('Select a module')
            ->getSearchResultsUsing(fn (string $search) =>
                Module::where('label', 'like', "%{$search}%")
                    ->orderBy('label')
                    ->pluck('label', 'id')
            )
            ->getOptionLabelUsing(fn ($value) =>
                Module::find($value)?->label
            )
            ->required(),

            Toggle::make('is_active')
                ->label('Active in Package')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')        
                ->label('Module')
                ->sortable()
                ->searchable(),

            BooleanColumn::make('pivot.is_active')
                ->label('Active'),

            ])
           ->headerActions([
               AttachAction::make()
                ->form([
                    Select::make('recordId')
                        ->label('Module')
                        ->searchable()
                        ->options(fn () => Module::orderBy('label')->pluck('label', 'id'))
                        ->getOptionLabelFromRecordUsing(fn (Module $record) => $record->label)
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Active in Package')
                            ->default(true),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),   
                Tables\Actions\DetachAction::make(), 
            ]);
    }

    protected function getRelationshipQuery()
    {
        return parent::getRelationshipQuery()->with('module');
    }

}

