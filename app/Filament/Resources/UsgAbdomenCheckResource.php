<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsgAbdomenCheckResource\Pages;
use App\Models\UsgAbdomenCheck;
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

class UsgAbdomenCheckResource extends Resource
{
    protected static ?string $model = UsgAbdomenCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan USG Abdomen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('participant_id')->label('Nama Peserta')->relationship('participant', 'name')->searchable()->preload()->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $p = Participant::find($state);
                                    $set('tgl_lahir', Carbon::parse($p->date_of_birth)->translatedFormat('j F Y'));
                                    $set('usia', Carbon::parse($p->date_of_birth)->age);
                                    $set('jenis_kelamin', $p->gender);
                                }
                            })->required(),
                        Forms\Components\TextInput::make('no_rm')->label('No. RM'),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                        Forms\Components\TextInput::make('instansi')->label('Instansi'),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Pelaksanaan')->default(now()),
                    ]),

                Section::make('Hasil Pemeriksaan USG Abdomen')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('hepar')->label('Hepar')->rows(3)
                            ->default('Ukuran normal, intensitas echoparenkim normal, sudut tajam, tepi regular, VP/VH normal, IHBD/EHBD tak tampak dilatasi, tak tampak nodul/kista/massa'),
                        Forms\Components\Textarea::make('gallbladder')->label('Gallbladder')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, tak tampak nodul / kista / massa / batu / sludge'),
                        Forms\Components\Textarea::make('lien')->label('Lien')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, tak tampak nodul/kista/massa'),
                        Forms\Components\Textarea::make('pankreas')->label('Pankreas')->rows(2)
                            ->default('Intensitas echoparenkim normal, tak tampak nodul/kista/massa/batu'),
                        Forms\Components\Textarea::make('ren_kanan')->label('Ren Kanan')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, batas sinus korteks tegas, tak tampak ektasis PCS, tak tampak batu/kista/massa'),
                        Forms\Components\Textarea::make('ren_kiri')->label('Ren Kiri')->rows(2)
                            ->default('Ukuran normal, intensitas echoparenkim normal, batas sinus korteks tegas, tak tampak ektasis PCS, tak tampak batu/kista/massa'),
                        Forms\Components\Textarea::make('vesica_urinaria')->label('Vesica Urinaria')->rows(2)
                            ->default('Terisi cukup urine, tak tampak massa/kalsifikasi'),
                        Forms\Components\Textarea::make('prostat')->label('Prostat')->rows(2)
                            ->default('Bentuk dan ukuran normal dengan volume estimasi <30 ml³, tak tampak massa/kalsifikasi'),
                        Forms\Components\Textarea::make('catatan_tambahan_1')->label(false)->placeholder('Catatan tambahan baris 1')
                            ->default('Tak tampak limfadenopati paraaorta')->columnSpanFull(),
                        Forms\Components\Textarea::make('catatan_tambahan_2')->label(false)->placeholder('Catatan tambahan baris 2')
                            ->default('Tak tampak echocairan bebas pada cavum abdomen dan cavum toraks bilateral')->columnSpanFull(),
                    ]),

                Section::make('Kesimpulan & Radiologist')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('kesimpulan')->label('Kesimpulan')
                            ->default('Hepar / Lien / Gallbladder / Pankreas / Ren kanan / Ren kiri / Vesica urinaria / Prostat tak tampak kelainan')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('radiologist')->label('Radiologist')->default('dr. Yockie Risvian Manfigiawan, Sp.Rad'),
                        Forms\Components\FileUpload::make('tanda_tangan')->label('Upload TTD Dokter')->image()->disk('public')->directory('ttd-usg'),
                    ]),

                Section::make('Lampiran Gambar Hasil USG (Untuk Halaman 2)')
                    ->schema([
                        Forms\Components\FileUpload::make('gambar_hasil_usg')->label(false)->image()->disk('public')->directory('hasil-usg')->required(),
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
}
