<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AudiometryCheckResource\Pages;
use App\Models\AudiometryCheck;
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
                            ->live() // Membuat field ini reaktif
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $participant = Participant::find($state);
                                    if ($participant) {
                                        $set('tanggal_lahir', Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'));
                                        $set('umur', Carbon::parse($participant->date_of_birth)->age);
                                        $set('jenis_kelamin', $participant->gender);
                                    }
                                }
                            })
                            ->required()
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
                            ->maxLength(255),
                    ]),

                Section::make('Hasil Pemeriksaan Audiometri')
                    ->schema([
                        Grid::make(2)->schema([
                            // HASIL TELINGA KANAN
                            Fieldset::make('Telinga Kanan (AD) - Air Conduction')
                                ->schema([
                                    Grid::make(4)->schema([
                                        Forms\Components\TextInput::make('ad_ac_250')->label('250 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_500')->label('500 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_1000')->label('1000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_2000')->label('2000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_3000')->label('3000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_4000')->label('4000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_6000')->label('6000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('ad_ac_8000')->label('8000 Hz')->numeric(),
                                    ]),
                                ]),
                            // HASIL TELINGA KIRI
                            Fieldset::make('Telinga Kiri (AS) - Air Conduction')
                                ->schema([
                                    Grid::make(4)->schema([
                                        Forms\Components\TextInput::make('as_ac_250')->label('250 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_500')->label('500 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_1000')->label('1000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_2000')->label('2000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_3000')->label('3000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_4000')->label('4000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_6000')->label('6000 Hz')->numeric(),
                                        Forms\Components\TextInput::make('as_ac_8000')->label('8000 Hz')->numeric(),
                                    ]),
                                ]),
                        ]),
                    ]),

                Section::make('Kesimpulan & Saran')
                    ->schema([
                        Forms\Components\TextInput::make('derajat_ad')
                            ->label('Derajat Ambang Dengar Kanan (AD)')
                            ->placeholder('Contoh: 20 dB'),
                        Forms\Components\TextInput::make('derajat_as')
                            ->label('Derajat Ambang Dengar Kiri (AS)')
                            ->placeholder('Contoh: 18,75 dB'),
                        Forms\Components\Textarea::make('kesimpulan')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('saran')
                            ->columnSpanFull(),
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

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
}
