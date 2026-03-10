<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsgAbdomenCheckResource\Pages;
use App\Models\Dokter;
use App\Models\McuResult;
use App\Models\UsgAbdomenCheck;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;

class UsgAbdomenCheckResource extends Resource
{
    protected static ?string $model = UsgAbdomenCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan USG Abdomen';

    protected static bool $shouldRegisterNavigation = false;

    private static function isFemale(?string $gender): bool
    {
        return strcasecmp(trim((string) $gender), 'Perempuan') === 0;
    }

    private static function organPelvisDefault(?string $gender): string
    {
        if (self::isFemale($gender)) {
            return 'Bentuk dan ukuran normal, tidak tampak massa/kista';
        }

        return 'Bentuk dan ukuran normal dengan volume estimasi < 30 ml3, tidak tampak massa/kalsifikasi';
    }

    private static function kesimpulanDefault(?string $gender): string
    {
        if (self::isFemale($gender)) {
            return 'Hepar/Lien/Gallbladder/Pankreas/Ren kanan/Ren kiri/Vesica urinaria/Uterus tidak tampak kelainan';
        }

        return 'Hepar/Lien/Gallbladder/Pankreas/Ren kanan/Ren kiri/Vesica urinaria/Prostat tidak tampak kelainan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('participant_id')
                            ->label('Nama Peserta')
                            ->relationship('participant', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $p = Participant::find($state);
                                    $set('tgl_lahir', Carbon::parse($p->date_of_birth)->translatedFormat('j F Y'));
                                    $set('usia', Carbon::parse($p->date_of_birth)->age);
                                    $set('jenis_kelamin', $p->gender);
                                    $set('instansi', $p->department);

                                    $set('prostat', self::organPelvisDefault($p->gender));
                                    $set('kesimpulan', self::kesimpulanDefault($p->gender));
                                } else {
                                    $set('tgl_lahir', null);
                                    $set('usia', null);
                                    $set('jenis_kelamin', null);
                                    $set('instansi', null);
                                    $set('prostat', null);
                                    $set('kesimpulan', null);
                                }
                            })
                            ->required()
                            ->default(request('participant_id'))
                            ->disabled(filled(request('participant_id'))),
                        Forms\Components\TextInput::make('no_rm')->label('No. RM')->required(),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                        Forms\Components\TextInput::make('instansi')->label('Instansi')->readOnly(),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Pelaksanaan')->default(now()),
                    ]),

                Section::make('Hasil Pemeriksaan USG Abdomen')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('hepar')->label('Hepar')->rows(3)
                            ->default('Ukuran normal, intensitas echoparenkim normal, sudut tajam, tepi reguler, VP/VH normal, IHBD/EHBD tidak tampak dilatasi, tidak tampak nodul/kista/massa')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Ukuran normal, intensitas echoparenkim normal, sudut tajam, tepi reguler, VP/VH normal, IHBD/EHBD tidak tampak dilatasi, tidak tampak nodul/kista/massa') : null),
                        Forms\Components\Textarea::make('gallbladder')->label('Gallbladder')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, tidak tampak nodul/kista/massa/batu/sludge')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Ukuran normal, intensitas echoparenkim normal, tidak tampak nodul/kista/massa/batu/sludge') : null),
                        Forms\Components\Textarea::make('lien')->label('Lien')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, tidak tampak nodul/kista/massa')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Ukuran normal, intensitas echoparenkim normal, tidak tampak nodul/kista/massa') : null),
                        Forms\Components\Textarea::make('pankreas')->label('Pankreas')->rows(2)
                            ->default('Intensitas echoparenkim normal, tidak tampak nodul/kista/massa/batu')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Intensitas echoparenkim normal, tidak tampak nodul/kista/massa/batu') : null),
                        Forms\Components\Textarea::make('ren_kanan')->label('Ren Kanan')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, batas sinus korteks tegas, tidak tampak ektasis PCS, tidak tampak batu/kista/massa')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Ukuran normal, intensitas echoparenkim normal, batas sinus korteks tegas, tidak tampak ektasis PCS, tidak tampak batu/kista/massa') : null),
                        Forms\Components\Textarea::make('ren_kiri')->label('Ren Kiri')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, batas sinus korteks tegas, tidak tampak ektasis PCS, tidak tampak batu/kista/massa')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Ukuran normal, intensitas echoparenkim normal, batas sinus korteks tegas, tidak tampak ektasis PCS, tidak tampak batu/kista/massa') : null),
                        Forms\Components\Textarea::make('vesica_urinaria')->label('Vesica Urinaria')->rows(2)
                            ->default('Terisi cukup urine, tidak tampak massa/kalsifikasi')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Terisi cukup urine, tidak tampak massa/kalsifikasi') : null),
                        Forms\Components\Textarea::make('prostat')
                            ->label(fn(Get $get): string => self::isFemale($get('jenis_kelamin')) ? 'Uterus' : 'Prostat')
                            ->rows(2)
                            ->helperText(fn(Get $get): string => self::isFemale($get('jenis_kelamin')) ? 'Untuk peserta perempuan.' : 'Untuk peserta laki-laki.')
                            ->default('Bentuk dan ukuran normal dengan volume estimasi < 30 ml3, tidak tampak massa/kalsifikasi')
                            ->afterStateHydrated(fn(Get $get, $component, $state) => blank($state) ? $component->state(self::organPelvisDefault($get('jenis_kelamin'))) : null),
                        Forms\Components\Textarea::make('catatan_tambahan_1')->label(false)->placeholder('Catatan tambahan baris 1')
                            ->default('Tidak tampak limfadenopati paraaorta')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Tidak tampak limfadenopati paraaorta') : null)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('catatan_tambahan_2')->label(false)->placeholder('Catatan tambahan baris 2')
                            ->default('Tak tampak echo cairan bebas pada cavum abdomen dan cavum toraks bilateral')
                            ->afterStateHydrated(fn($component, $state) => blank($state) ? $component->state('Tak tampak echo cairan bebas pada cavum abdomen dan cavum toraks bilateral') : null)
                            ->columnSpanFull(),
                    ]),

                Section::make('Kesimpulan & Radiologist')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('kesimpulan')->label('Kesimpulan')
                            ->helperText(fn(Get $get): string => self::isFemale($get('jenis_kelamin')) ? 'Untuk peserta perempuan.' : 'Untuk peserta laki-laki.')
                            ->default('Hepar/Lien/Gallbladder/Pankreas/Ren kanan/Ren kiri/Vesica urinaria/Prostat tidak tampak kelainan')
                            ->afterStateHydrated(fn(Get $get, $component, $state) => blank($state) ? $component->state(self::kesimpulanDefault($get('jenis_kelamin'))) : null)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('dokter_id')
                            ->label('Radiologist')
                            ->relationship('dokter', 'name', fn($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $dokter = Dokter::find($state);
                                $set('radiologist', $dokter?->name);
                                $set('tanda_tangan', $dokter?->tanda_tangan);
                            }),
                        Forms\Components\Placeholder::make('radiologist_preview')
                            ->label('Nama Dokter')
                            ->content(fn(Forms\Get $get): string => $get('radiologist') ?: '-'),
                        Forms\Components\Hidden::make('radiologist'),
                        Forms\Components\Hidden::make('tanda_tangan'),
                    ]),

                Section::make('Lampiran Gambar Hasil USG (Untuk Halaman 2)')
                    ->schema([
                        Forms\Components\FileUpload::make('gambar_hasil_usg_lampiran')
                            ->label('Lampiran Gambar Hasil USG')
                            ->helperText('Minimal 3 foto. Bisa upload lebih banyak sesuai kebutuhan (mis. sampai 12 foto atau lebih).')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->minFiles(3)
                            ->required()
                            ->disk('public')
                            ->directory('hasil-usg')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rm')->searchable(),
                Tables\Columns\TextColumn::make('participant.name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pemeriksaan')->date()->sortable(),
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')->color('gray')
                    ->url(fn(UsgAbdomenCheck $record): string => route('usg.print', $record))
                    ->openUrlInNewTab(),
                Action::make('mcu_result')
                    ->label('MCU Result')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->visible(fn(UsgAbdomenCheck $record): bool => McuResult::query()
                        ->where('participant_id', $record->participant_id)
                        ->exists())
                    ->url(function (UsgAbdomenCheck $record): string {
                        $mcuResultId = McuResult::query()
                            ->where('participant_id', $record->participant_id)
                            ->latest('id')
                            ->value('id');

                        return McuResultResource::getUrl('edit', ['record' => $mcuResultId]);
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsgAbdomenChecks::route('/'),
            'create' => Pages\CreateUsgAbdomenCheck::route('/create'),
            'edit' => Pages\EditUsgAbdomenCheck::route('/{record}/edit'),
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
