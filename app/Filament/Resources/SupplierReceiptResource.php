<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierReceiptResource\Pages;
use App\Filament\Resources\SupplierReceiptResource\RelationManagers;
use App\Models\Supplier;
use App\Models\SupplierReceipt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierReceiptResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Manajemen Aset';
    protected static ?string $label = 'Supplier';
    protected static ?string $pluralLabel = 'Supplier';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Supplier')
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('address')
                    ->label('Alamat')
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('address')->label('Alamat')->limit(40),
                Tables\Columns\TextColumn::make('phone')->label('Telepon'),
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
            'index' => Pages\ListSupplierReceipts::route('/'),
            'create' => Pages\CreateSupplierReceipt::route('/create'),
            'edit' => Pages\EditSupplierReceipt::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view supplier');
    }
}
