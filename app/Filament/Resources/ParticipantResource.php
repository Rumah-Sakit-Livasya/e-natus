<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class ParticipantResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\MedicalCheckUpCluster::class;

    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Medical Check Up';
    protected static ?string $label = 'Participant';
    protected static ?string $pluralLabel = 'Participants';
    protected static ?int $navigationSort = 1;

    // Tambahkan use jika belum ada:

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Proyek & Peserta')
                    ->schema([
                        // =================== KOMPONEN PROYEK REQUEST ===================
                        Select::make('project_request_id')
                            ->label('Proyek')
                            ->options(
                                \App\Models\ProjectRequest::orderByDesc('created_at')->pluck('name', 'id')
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            // Mengambil nilai 'project_request_id' dari URL
                            ->default(request('project_request_id'))
                            // Menonaktifkan field ini jika sudah terisi dari URL
                            ->disabled(filled(request('project_request_id')))
                            ->columnSpanFull(),
                        // ===============================================================

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
                                    ->label('Instansi')
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

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('note')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),

                // =================== TAMBAHKAN KOLOM INI ===================
                TextColumn::make('projectRequest.name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                // =========================================================

                Tables\Columns\TextColumn::make('employee_code')
                    ->label('No. Pegawai')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Instansi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Didaftarkan pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // =================== TAMBAHKAN FILTER INI ===================
                SelectFilter::make('project_request_id')
                    ->label('Filter Berdasarkan Proyek')
                    ->relationship('projectRequest', 'name')
                    ->searchable()
                    ->preload(),
                // ==========================================================
            ])
            ->actions([
                Action::make('print_summary')
                    ->label('Rekap Laporan')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('warning')
                    ->url(fn(Participant $record): string => route('participant.report.print', $record))
                    ->openUrlInNewTab(),

                ActionGroup::make([
                    Action::make('audiometry')
                        ->label('Audiometri')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->url(fn(Participant $record): string => AudiometryCheckResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('drug_test')
                        ->label('Tes Narkoba')
                        ->icon('heroicon-o-beaker')
                        ->url(fn(Participant $record): string => DrugTestResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('ekg')
                        ->label('EKG')
                        ->icon('heroicon-o-heart')
                        ->url(fn(Participant $record): string => EkgCheckResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('lab_check')
                        ->label('Lab Lengkap')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->url(fn(Participant $record): string => LabCheckResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('rontgen')
                        ->label('Rontgen')
                        ->icon('heroicon-o-viewfinder-circle')
                        ->url(fn(Participant $record): string => RontgenCheckResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('spirometry')
                        ->label('Spirometri')
                        ->icon('heroicon-o-scale')
                        ->url(fn(Participant $record): string => SpirometryCheckResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('treadmill')
                        ->label('Treadmill')
                        ->icon('heroicon-o-presentation-chart-line')
                        ->url(fn(Participant $record): string => TreadmillCheckResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('usg_abdomen')
                        ->label('USG Abdomen')
                        ->icon('heroicon-o-photo')
                        ->url(fn(Participant $record): string => UsgAbdomenCheckResource::getUrl('create', ['participant_id' => $record->id])),
                    Action::make('usg_mammae')
                        ->label('USG Mammae')
                        ->icon('heroicon-o-sparkles')
                        ->url(fn(Participant $record): string => UsgMammaeCheckResource::getUrl('create', ['participant_id' => $record->id])),
                ])
                    ->label('Pemeriksaan')
                    ->icon('heroicon-m-plus')
                    ->color('success')
                    ->dropdown(true),

                Tables\Actions\EditAction::make(),
                // Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'view' => Pages\ViewParticipant::route('/{record}'),
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view participant project');
    }
}
