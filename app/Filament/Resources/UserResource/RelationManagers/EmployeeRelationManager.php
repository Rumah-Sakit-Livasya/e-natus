<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class EmployeeRelationManager extends RelationManager
{
    protected static string $relationship = 'employee';
    protected static ?string $title = 'Data Pegawai';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nik')->required(),
            Forms\Components\TextInput::make('position')->label('Jabatan')->required(),
            Forms\Components\TextInput::make('phone')->label('Telepon'),
            Forms\Components\Textarea::make('address')->label('Alamat')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik'),
                Tables\Columns\TextColumn::make('position'),
                Tables\Columns\TextColumn::make('phone'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Pegawai'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
