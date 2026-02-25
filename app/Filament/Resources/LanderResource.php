<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanderResource\Pages;
use App\Filament\Resources\LanderResource\RelationManagers;
use App\Models\Lander;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LanderResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\AsetCluster::class;

    protected static ?string $model = Lander::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Lander';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $pluralModelLabel = 'Lander';
    protected static ?string $modelLabel = 'Lander';
    protected static ?int $navigationSort = 92;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nama Lander')
                ->required()
                ->maxLength(255),

            TextInput::make('code')
                ->label('Kode Lander')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                EditAction::make()->icon('heroicon-o-pencil')->tooltip('Edit'),
                DeleteAction::make()->icon('heroicon-o-trash')->tooltip('Hapus'),
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
            'index' => Pages\ListLanders::route('/'),
            'create' => Pages\CreateLander::route('/create'),
            'edit' => Pages\EditLander::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view landers');
    }
}
