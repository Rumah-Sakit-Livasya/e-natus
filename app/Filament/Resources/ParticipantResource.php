<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    // Mengatur ikon dan grup di menu sidebar
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen Proyek & Peserta';
    protected static ?int $navigationSort = 2; // Urutan di menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi Peserta')
                    ->schema([
                        // Menggunakan grid untuk layout 2 kolom
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
                                    ->options([
                                        'Belum Menikah' => 'Belum Menikah',
                                        'Menikah' => 'Menikah',
                                        'Cerai Hidup' => 'Cerai Hidup',
                                        'Cerai Mati' => 'Cerai Mati',
                                    ])
                                    ->required(),
                            ]),

                        // Kolom ini akan memakan lebar penuh
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('note')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee_code')
                    ->label('No. Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->label('Departemen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Didaftarkan pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan default
            ])
            ->filters([
                // Anda bisa menambahkan filter di sini jika perlu
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(), // Tambahkan action untuk melihat detail
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
            // Jika Anda ingin menampilkan daftar MCU yang diikuti peserta ini di halaman detail,
            // Anda bisa membuat RelationManager di sini.
        ];
    }

    public static function getPages(): array
    {
        // Halaman-halaman ini sudah dibuat otomatis oleh opsi --generate
        return [
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'view' => Pages\ViewParticipant::route('/{record}'), // Menambahkan route untuk View
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}
