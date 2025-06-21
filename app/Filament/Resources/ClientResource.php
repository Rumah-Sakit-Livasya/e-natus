<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Klien';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Klien';
    protected static ?string $pluralModelLabel = 'Data Klien';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Nama Klien')->required(),
            TextInput::make('pic')->label('PIC')->required(),
            Select::make('region_id')
                ->label('Wilayah')
                ->options(Region::all()->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            TextInput::make('phone')->label('Nomor Telepon')->tel(),
            TextInput::make('email')->label('Email')->email(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama Klien')->searchable()->sortable(),
            TextColumn::make('pic')->label('PIC')->searchable(),
            TextColumn::make('region.name')->label('Wilayah')->sortable(),
            TextColumn::make('phone')->label('Telepon'),
            TextColumn::make('email')->label('Email'),
        ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }

        return auth()->user()->can('view clients');
    }
}
