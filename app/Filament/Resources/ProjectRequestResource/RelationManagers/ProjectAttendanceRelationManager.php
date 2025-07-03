<?php

namespace App\Filament\Resources\ProjectRequestResource\RelationManagers;

use App\Models\Employee;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Facades\Storage;
use Livewire\TemporaryUploadedFile;

class ProjectAttendanceRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';
    protected static ?string $title = 'Absensi';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('employee_id')
                ->label('Karyawan')
                ->options(User::pluck('name', 'id'))
                ->searchable()
                ->required(),

            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required(),

            TextInput::make('lokasi_maps')
                ->label('Lokasi (Koordinat GPS)')
                ->required(),

            FileUpload::make('foto')
                ->label('Ambil Foto')
                ->image()
                ->directory('absensi')
                ->imagePreviewHeight('100')
                ->required(),

            Textarea::make('keterangan')->label('Keterangan')->rows(2)->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.nik')->label('NIK'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('lokasi_maps')->limit(20),
                Tables\Columns\ImageColumn::make('foto')->label('Foto'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Absen'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada absensi');
    }
}
