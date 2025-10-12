<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DrugTestResource\Pages;
use App\Models\DrugTest;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;

class DrugTestResource extends Resource
{
    protected static ?string $model = DrugTest::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Tes Narkoba';

    public static function form(Form $form): Form
    {
        $options = ['Negatif' => 'Negatif', 'Positif' => 'Positif'];

        return $form
            ->schema([
                Section::make('Informasi Pasien & Pemeriksaan')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('participant_id')
                            ->label('Nama Peserta')
                            ->relationship('participant', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $participant = Participant::find($state);
                                    if ($participant) {
                                        $set('nik', $participant->employee_code); // Menggunakan employee_code sebagai NIK
                                        $set('tgl_lahir', Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'));
                                        $set('umur', Carbon::parse($participant->date_of_birth)->age);
                                        $set('j_kel', $participant->gender);
                                    }
                                }
                            })
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('nik')
                            ->label('No. NIK')
                            ->readOnly(),

                        Forms\Components\TextInput::make('department')
                            ->label('PT / Dept')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('tgl_lahir')
                            ->label('Tgl. Lahir')
                            ->readOnly(),

                        Forms\Components\TextInput::make('umur')
                            ->label('Umur')
                            ->suffix('Tahun')
                            ->readOnly(),

                        Forms\Components\TextInput::make('j_kel')
                            ->label('J. Kel')
                            ->readOnly(),

                        Forms\Components\TextInput::make('no_mcu')
                            ->label('No. MCU')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')
                            ->default(now())
                            ->required(),
                    ]),

                Section::make('Hasil Pemeriksaan Tes Narkoba')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('amphetamine')->options($options)->default('Negatif')->required(),
                        Forms\Components\Select::make('metamphetamine')->options($options)->default('Negatif')->required(),
                        Forms\Components\Select::make('cocaine')->options($options)->default('Negatif')->required(),
                        Forms\Components\Select::make('thc')->label('THC')->options($options)->default('Negatif')->required(),
                        Forms\Components\Select::make('morphine')->options($options)->default('Negatif')->required(),
                        Forms\Components\Select::make('benzodiazepine')->options($options)->default('Negatif')->required(),
                    ]),

                Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('analis_kesehatan')
                            ->label('Analis Kesehatan')
                            ->placeholder('Contoh: Azzam tsaqif f')
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_mcu')->searchable(),
                Tables\Columns\TextColumn::make('participant.name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pemeriksaan')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(DrugTest $record): string => route('drug-test.print', $record))
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
            'index' => Pages\ListDrugTests::route('/'),
            'create' => Pages\CreateDrugTest::route('/create'),
            // 'view' => Pages\ViewDrugTest::route('/{record}'),
            'edit' => Pages\EditDrugTest::route('/{record}/edit'),
        ];
    }
}
