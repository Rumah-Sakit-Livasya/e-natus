<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class NotificationResource extends Resource
{
    // Gunakan model notifikasi bawaan Laravel
    protected static ?string $model = DatabaseNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Semua Notifikasi';
    protected static ?string $modelLabel = 'Notifikasi';
    protected static ?string $pluralModelLabel = 'Semua Notifikasi';

    // Tempatkan di bawah grup menu yang Anda inginkan
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 100; // Posisikan di bawah

    /**
     * INI ADALAH BAGIAN PALING PENTING.
     * Kita memodifikasi query dasar agar setiap user HANYA bisa melihat notifikasinya sendiri.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('notifiable_id', auth()->id());
    }

    // Kita tidak akan membuat notifikasi secara manual, jadi form ini untuk tampilan detail.
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Notifikasi')
                    ->schema([
                        Placeholder::make('Pesan')
                            ->content(fn(DatabaseNotification $record): ?string => $record->data['message'] ?? '-'),

                        Placeholder::make('Waktu Diterima')
                            ->content(fn(DatabaseNotification $record): string => $record->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm')),

                        View::make('filament.forms.components.notification-link')
                            ->viewData(fn(DatabaseNotification $record): array => [
                                'url' => $record->data['url'] ?? null,
                            ])
                            ->visible(fn(DatabaseNotification $record): bool => isset($record->data['url'])),

                    ])->columns(1), // Tambahkan ini agar setiap item mengambil satu baris penuh
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read_at')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-bell-alert')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn(DatabaseNotification $record): string => $record->read_at ? 'Sudah dibaca' : 'Belum dibaca'),

                TextColumn::make('data.message')
                    ->label('Pesan')
                    ->limit(60)
                    // PERBAIKAN DI SINI: Gunakan closure untuk mencari di kolom JSON
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereJsonContains('data->message', $search);
                    }),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // Tombol untuk melihat detail notifikasi
                Tables\Actions\ViewAction::make()
                    // Tandai sudah dibaca secara otomatis saat notifikasi dilihat
                    ->after(fn(DatabaseNotification $record) => $record->markAsRead()),

                // Tombol untuk menandai sudah dibaca tanpa membuka detail
                Tables\Actions\Action::make('markAsRead')
                    ->label('Tandai Dibaca')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (DatabaseNotification $record) {
                        $record->markAsRead();
                        FilamentNotification::make()->title('Notifikasi telah ditandai sebagai sudah dibaca')->success()->send();
                    })
                    ->visible(fn(DatabaseNotification $record): bool => $record->read_at === null),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('markAsRead')
                    ->label('Tandai Semua sebagai Dibaca')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($records) {
                        $records->each->markAsRead();
                        FilamentNotification::make()->title('Notifikasi yang dipilih telah ditandai sebagai sudah dibaca')->success()->send();
                    }),
            ]);
    }

    // Kita tidak memerlukan halaman create dan edit, hanya daftar dan detail
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'view' => Pages\ViewNotification::route('/{record}'),
        ];
    }

    // Sembunyikan titik notifikasi di menu sidebar untuk halaman ini
    public static function getNavigationBadge(): ?string
    {
        return null;
    }
}
