<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RontgenCheckResource\Pages;
use App\Models\RontgenCheck;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;

class RontgenCheckResource extends Resource
{
    protected static ?string $model = RontgenCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-viewfinder-circle';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan Rontgen';

    public static function form(Form $form): Form
    {
        $defaultTemuan = "- Apex pulmo bilateral tidak ada infiltrate\n"
            . "- Corakan bronchovasculer normal\n"
            . "- Fissura minor menebal\n"
            . "- Sinus costophrenicus lancip\n"
            . "- Diafragma licin\n"
            . "- CTR < 50%\n"
            . "- Tulang tulang baik";

        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('participant_id')->label('Nama Peserta')->relationship('participant', 'name')->searchable()->preload()->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $p = Participant::find($state);
                                    $set('tgl_lahir', Carbon::parse($p->date_of_birth)->translatedFormat('j F Y'));
                                    $set('usia', Carbon::parse($p->date_of_birth)->age);
                                    $set('jenis_kelamin', $p->gender);
                                    $set('instansi', $p->department); // Tambahkan ini
                                } else {
                                    $set('tgl_lahir', null);
                                    $set('usia', null);
                                    $set('jenis_kelamin', null);
                                    $set('instansi', null);
                                }
                            })
                            ->required()
                            // =======================================================
                            //           PERUBAHAN UTAMA ADA DI DUA BARIS INI
                            // =======================================================
                            ->default(request('participant_id'))
                            ->disabled(filled(request('participant_id')))
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('no_rontgen')->label('No. Rontgen'),
                        Forms\Components\TextInput::make('no_rm')->label('No. RM'),
                        Forms\Components\TextInput::make('instansi')->label('Instansi'),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Pelaksanaan')->default(now()),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                    ]),

                Section::make('Hasil Pemeriksaan')
                    ->schema([
                        Forms\Components\Textarea::make('temuan')
                            ->label('Yth, TS.')
                            ->rows(8),
                        Forms\Components\TextInput::make('kesan')
                            ->label('Kesan'),
                    ]),

                Section::make('Radiologist & Lampiran')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('radiologist')->label('Radiologist')->default('dr. Firdaus, Sp. Rad'),
                        Forms\Components\FileUpload::make('tanda_tangan')->label('Upload TTD Dokter')->image()->disk('public')->directory('ttd-rontgen'),
                        Forms\Components\FileUpload::make('gambar_hasil_rontgen')->label('Upload Gambar Hasil Rontgen')->image()->disk('public')->directory('hasil-rontgen')->required()->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rontgen')->searchable(),
                Tables\Columns\TextColumn::make('participant.name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pemeriksaan')->date()->sortable(),
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')->color('gray')
                    ->url(fn(RontgenCheck $record): string => route('rontgen.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRontgenChecks::route('/'),
            'create' => Pages\CreateRontgenCheck::route('/create'),
            'edit' => Pages\EditRontgenCheck::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return true;
        }
        return $user->can('view hasil mcu');
    }
}
