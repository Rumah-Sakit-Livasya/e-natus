<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AudiometryCheckResource\Pages;
use App\Models\AudiometryCheck;
use App\Models\Dokter;
use App\Models\McuResult;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Actions\Action;

class AudiometryCheckResource extends Resource
{
    protected static ?string $model = AudiometryCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Pemeriksaan'; // Sesuaikan grup menu
    protected static ?string $pluralModelLabel = 'Pemeriksaan Audiometri';

    protected static bool $shouldRegisterNavigation = false;

    private static function isNumericValue(mixed $value): bool
    {
        return $value !== null && $value !== '' && is_numeric($value);
    }

    private static function calculateAverage(Get $get, string $prefix): ?float
    {
        $values = [
            $get("{$prefix}_500"),
            $get("{$prefix}_1000"),
            $get("{$prefix}_2000"),
            $get("{$prefix}_4000"),
        ];

        foreach ($values as $value) {
            if (! self::isNumericValue($value)) {
                return null;
            }
        }

        return array_sum(array_map('floatval', $values)) / 4;
    }

    private static function formatAverageValue(?float $average): string
    {
        if ($average === null) {
            return '-';
        }
        return number_format($average, 2, '.', '');
    }

    private static function updateHearingThreshold(Get $get, Set $set): void
    {
        $adAverage = self::calculateAverage($get, 'ad_ac');
        $asAverage = self::calculateAverage($get, 'as_ac');
        $adBcAverage = self::calculateAverage($get, 'ad_bc');
        $asBcAverage = self::calculateAverage($get, 'as_bc');

        $set('derajat_ad', self::formatAverageValue($adAverage));
        $set('derajat_as', self::formatAverageValue($asAverage));
        $set('derajat_ad_bc', self::formatAverageValue($adBcAverage));
        $set('derajat_as_bc', self::formatAverageValue($asBcAverage));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('no_rm')
                            ->label('No. RM')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')
                            ->label('Tanggal Pelaksanaan')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('participant_id')
                            ->label('Nama Peserta')
                            ->relationship('participant', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            // afterStateUpdated tetap diperlukan jika user mengganti pilihan secara manual
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $participant = Participant::find($state);
                                    if ($participant) {
                                        $set('tanggal_lahir', Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'));
                                        $set('umur', Carbon::parse($participant->date_of_birth)->age);
                                        $set('jenis_kelamin', $participant->gender);
                                        $set('instansi', $participant->department);
                                    }
                                } else {
                                    // Kosongkan field jika pilihan dihapus
                                    $set('tanggal_lahir', null);
                                    $set('umur', null);
                                    $set('jenis_kelamin', null);
                                    $set('instansi', null);
                                }
                            })
                            ->required()
                            ->default(request('participant_id'))
                            ->disabled(filled(request('participant_id')))
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->readOnly(),

                        Forms\Components\TextInput::make('umur')
                            ->label('Usia')
                            ->suffix('Tahun')
                            ->readOnly(),

                        Forms\Components\TextInput::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->readOnly(),

                        Forms\Components\TextInput::make('instansi')
                            ->label('Instansi')
                            ->readOnly()
                            ->maxLength(255),
                    ]),

                Section::make('Hasil Pemeriksaan Audiometri')
                    ->schema([
                        Grid::make(2)->schema([
                            // HASIL TELINGA KANAN
                            Fieldset::make('Telinga Kanan (AD) - Air Conduction')
                                ->schema([
                                    Grid::make(8)->schema([
                                        Forms\Components\TextInput::make('ad_ac_250')->label('250 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_500')
                                            ->label('500 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_ac_1000')
                                            ->label('1000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_ac_2000')
                                            ->label('2000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_ac_3000')->label('3000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_4000')
                                            ->label('4000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_ac_6000')->label('6000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_8000')->label('8000 Hz')->numeric(),
                                    ]),
                                ]),
                            // HASIL TELINGA KIRI
                            Fieldset::make('Telinga Kiri (AS) - Air Conduction')
                                ->schema([
                                    Grid::make(8)->schema([
                                        Forms\Components\TextInput::make('as_ac_250')->label('250 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_500')
                                            ->label('500 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_ac_1000')
                                            ->label('1000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_ac_2000')
                                            ->label('2000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_ac_3000')->label('3000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_4000')
                                            ->label('4000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_ac_6000')->label('6000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_8000')->label('8000 Hz')->numeric(),
                                    ]),
                                ]),
                            Fieldset::make('Telinga Kanan (AD) - Bone Conduction (Opsional)')
                                ->schema([
                                    Grid::make(8)->schema([
                                        Forms\Components\TextInput::make('ad_bc_250')
                                            ->label('250 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_bc_500')
                                            ->label('500 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_bc_1000')
                                            ->label('1000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_bc_2000')
                                            ->label('2000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_bc_3000')
                                            ->label('3000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_bc_4000')
                                            ->label('4000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_bc_6000')
                                            ->label('6000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('ad_bc_8000')
                                            ->label('8000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                    ]),
                                ]),
                            Fieldset::make('Telinga Kiri (AS) - Bone Conduction (Opsional)')
                                ->schema([
                                    Grid::make(8)->schema([
                                        Forms\Components\TextInput::make('as_bc_250')
                                            ->label('250 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_bc_500')
                                            ->label('500 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_bc_1000')
                                            ->label('1000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_bc_2000')
                                            ->label('2000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_bc_3000')
                                            ->label('3000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_bc_4000')
                                            ->label('4000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_bc_6000')
                                            ->label('6000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                        Forms\Components\TextInput::make('as_bc_8000')
                                            ->label('8000 Hz')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateHearingThreshold($get, $set)),
                                    ]),
                                ]),
                        ]),
                    ]),

                Section::make('Kesimpulan & Saran')
                    ->schema([
                        Forms\Components\TextInput::make('derajat_ad')
                            ->label('Derajat Ambang Dengar Kanan (AD)')
                            ->suffix('dB')
                            ->readOnly(),
                        Forms\Components\TextInput::make('derajat_as')
                            ->label('Derajat Ambang Dengar Kiri (AS)')
                            ->suffix('dB')
                            ->readOnly(),
                        Forms\Components\TextInput::make('derajat_ad_bc')
                            ->label('Derajat Ambang Dengar Kanan (AD) - Bone Conduction')
                            ->suffix('dB')
                            ->readOnly(),
                        Forms\Components\TextInput::make('derajat_as_bc')
                            ->label('Derajat Ambang Dengar Kiri (AS) - Bone Conduction')
                            ->suffix('dB')
                            ->readOnly(),
                        Forms\Components\Textarea::make('kesimpulan')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('saran')
                            ->columnSpanFull(),
                    ]),

                Section::make('Dokter & Tanda Tangan')
                    ->schema([
                        Forms\Components\Select::make('dokter_id')
                            ->label('Dokter Pemeriksa')
                            ->relationship('dokter', 'name', fn($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $dokter = Dokter::find($state);
                                $set('tanda_tangan', $dokter?->tanda_tangan);
                            }),
                        Forms\Components\Hidden::make('tanda_tangan'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rm')
                    ->label('No. RM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('participant.name')
                    ->label('Nama Peserta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pemeriksaan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('instansi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(AudiometryCheck $record): string => route('audiometry.print', $record))
                    ->openUrlInNewTab(),
                Action::make('mcu_result')
                    ->label('MCU Result')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->visible(fn(AudiometryCheck $record): bool => McuResult::query()
                        ->where('participant_id', $record->participant_id)
                        ->exists())
                    ->url(function (AudiometryCheck $record): string {
                        $mcuResultId = McuResult::query()
                            ->where('participant_id', $record->participant_id)
                            ->latest('id')
                            ->value('id');

                        return McuResultResource::getUrl('edit', ['record' => $mcuResultId]);
                    }),

                Action::make('edit_result_revision')
                    ->label('Edit Hasil (Revisi)')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn(AudiometryCheck $record): string => static::getUrl('create', [
                        'participant_id' => $record->participant_id,
                        'revise_from' => $record->id,
                    ])),

                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListAudiometryChecks::route('/'),
            'create' => Pages\CreateAudiometryCheck::route('/create'),
            // 'view' => Pages\ViewAudiometryCheck::route('/{record}'),
            'edit' => Pages\EditAudiometryCheck::route('/{record}/edit'),
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
