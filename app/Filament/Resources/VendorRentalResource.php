<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorRentalResource\Pages;
use App\Filament\Resources\VendorRentalResource\RelationManagers;
use App\Models\VendorRental;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorRentalResource extends Resource
{
    protected static ?string $model = VendorRental::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Alat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->label('Harga Sewa')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('qty')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('unit')
                    ->label('Satuan')
                    ->placeholder('e.g. DAY, HOUR, PCS')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Alat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Sewa')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Satuan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListVendorRentals::route('/'),
            'create' => Pages\CreateVendorRental::route('/create'),
            'edit' => Pages\EditVendorRental::route('/{record}/edit'),
        ];
    }
}
