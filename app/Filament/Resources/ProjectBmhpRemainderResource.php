<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\BmhpCluster;
use App\Filament\Resources\ProjectBmhpRemainderResource\Pages;
use App\Models\RabClosingBmhpItem;
use App\Models\RabClosing;
use App\Models\Bmhp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectBmhpRemainderResource extends Resource
{
    protected static ?string $model = RabClosingBmhpItem::class;

    protected static ?string $cluster = BmhpCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'BHP Sisa Project';

    protected static ?string $pluralModelLabel = 'BHP Sisa Project';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Updating Sisa BHP')
                    ->description('Pastikan data ini sesuai dengan catatan fisik logistik.')
                    ->schema([
                        Forms\Components\Placeholder::make('project_name')
                            ->label('Proyek')
                            ->content(fn($record) => $record?->rabClosing?->projectRequest?->name ?? '-'),
                        Forms\Components\Placeholder::make('item_name')
                            ->label('Nama Barang')
                            ->content(fn($record) => $record?->name ?? '-'),
                        Forms\Components\Placeholder::make('jumlah_rencana')
                            ->label('Jumlah Keluar (Rencana)')
                            ->content(fn($record) => $record?->jumlah_rencana ?? '-'),
                        Forms\Components\TextInput::make('jumlah_sisa')
                            ->label('Jumlah Sisa (Kembali)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->helperText('Jumlah barang yang kembali ke logistik.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rabClosing.projectRequest.name')
                    ->label('Proyek')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_rencana')
                    ->label('Rencana')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_sisa')
                    ->label('Sisa (Kembali)')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Input Terakhir')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rab_closing_id')
                    ->label('Proyek')
                    ->options(RabClosing::with('projectRequest')->get()->pluck('projectRequest.name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Update Sisa')
                    ->modalHeading('Update Sisa Barang Project'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectBmhpRemainders::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user->can('view bmhp');
    }
}
