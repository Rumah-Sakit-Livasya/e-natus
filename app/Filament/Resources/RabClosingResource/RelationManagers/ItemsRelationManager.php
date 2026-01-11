<?php

namespace App\Filament\Resources\RabClosingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        // Form ini hanya untuk melihat, karena data disalin secara otomatis
        return $form
            ->schema([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('qty')
                    ->numeric()
                    ->required(),
                TextInput::make('satuan')
                    ->required(),
                TextInput::make('harga_satuan')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('total_anggaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('description'),
                TextColumn::make('qty'),
                TextColumn::make('satuan'),
                TextColumn::make('harga_satuan')->money('IDR'),
                TextColumn::make('total_anggaran')->money('IDR')->label('Total Anggaran'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // Nonaktifkan karena dibuat otomatis
            ])
            ->actions([
                // Tables\Actions\EditAction::make(), // Nonaktifkan
                // Tables\Actions\DeleteAction::make(), // Nonaktifkan
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
