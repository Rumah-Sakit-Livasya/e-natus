<?php

namespace App\Filament\Resources\ProjectRequestResource\RelationManagers;

use App\Models\RencanaAnggaranBiaya;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class RealisationRabItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'realisationRabItems';

    protected static ?string $title = 'Realisasi RAB';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('rencana_anggaran_biaya_id')
                ->label('Item RAB')
                ->options(fn($livewire) => RencanaAnggaranBiaya::where('project_request_id', $livewire->ownerRecord->id)
                    ->pluck('description', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('description')->label('Uraian')->required(),
            Forms\Components\TextInput::make('qty')->numeric()->required(),
            Forms\Components\TextInput::make('harga')->numeric()->required(),
            Forms\Components\TextInput::make('total')
                ->numeric()
                ->required()
                ->default(fn($get) => (int) $get('qty') * (int) $get('harga')),
            Forms\Components\DatePicker::make('tanggal_realisasi')->required(),
            Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('rencanaAnggaranBiaya.description')->label('Item RAB'),
            Tables\Columns\TextColumn::make('description'),
            Tables\Columns\TextColumn::make('qty'),
            Tables\Columns\TextColumn::make('harga')->money('IDR', true),
            Tables\Columns\TextColumn::make('total')->money('IDR', true),
            Tables\Columns\TextColumn::make('selisih')
                ->label('Selisih')
                ->state(function ($record) {
                    $rab = $record->rabItem;
                    return $rab ? $rab->total - $record->total : '-';
                })
                ->color(fn($state) => $state < 0 ? 'danger' : 'success')
                ->money('IDR', true),

            Tables\Columns\TextColumn::make('tanggal_realisasi')->date(),
        ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected $listeners = ['openCreateRealisationFromRab' => 'handleOpen'];

    public function handleOpen(string $target)
    {
        if ($target === static::getRelationshipName()) {
            $this->mountTableAction('create');
        }
    }
}
