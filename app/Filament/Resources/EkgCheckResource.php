<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EkgCheckResource\Pages;
use App\Models\EkgCheck;
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

class EkgCheckResource extends Resource
{
    protected static ?string $model = EkgCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan EKG';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('participant_id')
                            ->label('Nama Peserta')
                            ->relationship('participant', 'name')
                            ->searchable()->preload()->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $p = Participant::find($state);
                                    $set('tgl_lahir', Carbon::parse($p->date_of_birth)->translatedFormat('j F Y'));
                                    $set('usia', Carbon::parse($p->date_of_birth)->age);
                                    $set('jenis_kelamin', $p->gender);
                                }
                            })
                            ->required()->columnSpan(2),

                        Forms\Components\TextInput::make('no_rm')->label('No. RM'),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                        Forms\Components\TextInput::make('instansi')->label('Instansi'),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Pelaksanaan')->default(now()),
                    ]),

                Section::make('Hasil Interpretasi EKG')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('irama')->label('Irama')->default('Sinus Ritme'),
                        Forms\Components\TextInput::make('heart_rate')->label('Heart Rate')->placeholder('x/i reg'),
                        Forms\Components\TextInput::make('axis')->label('Axis')->default('Normal'),
                        Forms\Components\TextInput::make('pr_interval')->label('PR Interval')->placeholder('0.16"'),
                        Forms\Components\TextInput::make('qrs_duration')->label('QRS Duration')->placeholder('0.08"'),
                        Forms\Components\TextInput::make('gel_t')->label('Gel T')->default('Normal'),
                        Forms\Components\TextInput::make('st_t_changes')->label('ST-T Changes')->default('-'),
                        Forms\Components\TextInput::make('kelainan')->label('Kelainan')->default('-'),
                        Forms\Components\Textarea::make('kesimpulan')->label('Kesimpulan')->default('Normal EKG')->columnSpanFull(),
                    ]),

                Section::make('Dokter Pemeriksa & Tanda Tangan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('dokter_pemeriksa')
                            ->label('Nama Dokter (Cardiologist)')
                            ->default('dr. Giky Karwiky, Sp.JP(K)'),
                        Forms\Components\FileUpload::make('tanda_tangan')
                            ->label('Upload TTD Dokter (.png)')
                            ->image()->disk('public')->directory('ttd-ekg')
                            ->imagePreviewHeight('100'),
                    ]),

                Section::make('Gambar Hasil EKG (Untuk Halaman 2)')
                    ->schema([
                        Forms\Components\FileUpload::make('gambar_hasil_ekg')
                            ->label('Upload Gambar Hasil EKG')
                            ->image()->disk('public')->directory('hasil-ekg')
                            ->required()->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rm')->searchable(),
                Tables\Columns\TextColumn::make('participant.name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pemeriksaan')->date()->sortable(),
                Tables\Columns\TextColumn::make('kesimpulan')->limit(40),
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')->color('gray')
                    ->url(fn(EkgCheck $record): string => route('ekg.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array // Sederhanakan navigasi
    {
        return [
            'index' => Pages\ListEkgChecks::route('/'),
            'create' => Pages\CreateEkgCheck::route('/create'),
            'edit' => Pages\EditEkgCheck::route('/{record}/edit'),
        ];
    }
}
