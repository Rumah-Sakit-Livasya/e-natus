<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceChangeRequestResource\Pages;
use App\Models\PriceChangeRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PriceChangeRequestResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\ProjectCluster::class;

    protected static ?string $model = PriceChangeRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Pengajuan';
    protected static ?string $navigationLabel = 'Price Change Requests';
    protected static ?string $pluralModelLabel = 'Price Change Requests';
    protected static ?int $navigationSort = 51;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('current_price')
                    ->label('Current Price')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\TextInput::make('requested_price')
                    ->label('Requested Price')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\Textarea::make('reason')
                    ->label('Reason')
                    ->disabled()
                    ->rows(3),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->disabled(),
                Forms\Components\Textarea::make('review_notes')
                    ->label('Review Notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Requested By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rabOperasionalItem.description')
                    ->label('Item')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('current_price')
                    ->label('Current Price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('requested_price')
                    ->label('Requested Price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(PriceChangeRequest $record) => $record->status === 'pending' && auth()->user()->hasAnyRole(['super-admin', 'owner']))
                    ->form([
                        Forms\Components\Textarea::make('review_notes')
                            ->label('Notes (Optional)')
                            ->rows(3),
                    ])
                    ->action(function (PriceChangeRequest $record, array $data) {
                        // Update the price
                        $record->rabOperasionalItem->update([
                            'harga_sewa' => $record->requested_price,
                            'total' => $record->rabOperasionalItem->qty_aset * $record->requested_price,
                        ]);

                        // Update request status
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                            'review_notes' => $data['review_notes'] ?? null,
                        ]);

                        // Notify requester
                        if ($record->requester) {
                            $record->requester->notify(
                                new \App\Notifications\PriceChangeResponseNotification($record)
                            );
                        }

                        Notification::make()
                            ->title('Price change approved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(PriceChangeRequest $record) => $record->status === 'pending' && auth()->user()->hasAnyRole(['super-admin', 'owner']))
                    ->form([
                        Forms\Components\Textarea::make('review_notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (PriceChangeRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                            'review_notes' => $data['review_notes'],
                        ]);

                        // Notify requester
                        if ($record->requester) {
                            $record->requester->notify(
                                new \App\Notifications\PriceChangeResponseNotification($record)
                            );
                        }

                        Notification::make()
                            ->title('Price change rejected')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPriceChangeRequests::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['super-admin', 'owner']);
    }
}
