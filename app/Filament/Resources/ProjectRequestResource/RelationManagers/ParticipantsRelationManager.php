<?php

namespace App\Filament\Resources\ProjectRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Peserta')
                    ->schema([
                        // TAMBAHKAN KOMPONEN UPLOAD FOTO DI SINI
                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto Peserta')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('participant-photos')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('employee_code')
                                    ->label('Nomor Pegawai / NIK')
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Tanggal Lahir')
                                    ->required(),

                                Forms\Components\TextInput::make('department')
                                    ->label('Departemen / Bagian')
                                    ->maxLength(255),

                                Forms\Components\Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'Laki-laki' => 'Laki-laki',
                                        'Perempuan' => 'Perempuan',
                                    ])
                                    ->required(),

                                Forms\Components\Select::make('marital_status')
                                    ->label('Status Pernikahan')
                                    // Menggunakan opsi yang sama dengan Resource utama
                                    ->options([
                                        'Belum Menikah' => 'Belum Menikah',
                                        'Menikah' => 'Menikah',
                                        'Cerai Hidup' => 'Cerai Hidup',
                                        'Cerai Mati' => 'Cerai Mati',
                                    ])
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->columnSpanFull(),

                        // Menambahkan field catatan agar konsisten
                        Forms\Components\Textarea::make('note')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // TAMBAHKAN KOLOM GAMBAR DI SINI
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Peserta')
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee_code')
                    ->label('No. Pegawai')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Departemen')
                    ->searchable(),

                Tables\Columns\TextColumn::make('gender')
                    ->label('Jenis Kelamin'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Menambahkan tombol View
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
