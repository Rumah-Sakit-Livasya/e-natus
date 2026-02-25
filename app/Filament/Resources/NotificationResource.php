<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Aset;
use App\Models\BmhpPurchase;
use App\Models\BmhpStockOpname;
use App\Models\ProjectRequest;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class NotificationResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\UserManagementCluster::class;

    public static ?string $model = DatabaseNotification::class;
    public static ?string $navigationIcon = 'heroicon-o-bell-alert';
    public static ?string $navigationGroup = 'User Management';
    public static ?string $navigationLabel = 'Semua Notifikasi';
    public static ?string $pluralModelLabel = 'Semua Notifikasi';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('notifiable_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Notifikasi')
                    ->schema([
                        Placeholder::make('Pesan')->content(fn(DatabaseNotification $record): ?string => $record->data['message'] ?? '-'),
                        Placeholder::make('Waktu Diterima')->content(fn(DatabaseNotification $record): string => $record->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm')),
                        Placeholder::make('Link')
                            ->content(function (DatabaseNotification $record) {
                                $url = $record->data['url'] ?? null;
                                return $url ? new \Illuminate\Support\HtmlString("<a href='{$url}' target='_blank' class='text-primary-600 hover:underline'>Klik untuk membuka detail pengajuan</a>") : '-';
                            })
                            ->visible(fn(DatabaseNotification $record): bool => isset($record->data['url'])),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('read_at')->label('Status')->boolean()->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-bell-alert')->trueColor('success')->falseColor('warning'),
                TextColumn::make('data.message')->label('Pesan')->limit(60)->searchable(query: fn(Builder $query, string $search): Builder => $query->whereJsonContains('data->message', $search)),
                TextColumn::make('created_at')->label('Waktu')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // ==========================================================
                // ▼▼▼ TOMBOL AKSI DI HALAMAN INDEX (TABEL) ▼▼▼
                // ==========================================================
                Tables\Actions\ViewAction::make()->after(fn(DatabaseNotification $record) => $record->markAsRead()),

                // Tampilkan tombol approve/reject untuk stockopname DAN projectrequest
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(DatabaseNotification $notification) => static::handleApproval($notification, 'approved'))
                    ->visible(function (DatabaseNotification $notification): bool {
                        return static::isActionable($notification)
                            && in_array($notification->data['record_model'] ?? null, [
                                BmhpStockOpname::class,
                                BmhpPurchase::class,
                                ProjectRequest::class,
                            ]);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(DatabaseNotification $notification) => static::handleApproval($notification, 'rejected'))
                    ->visible(function (DatabaseNotification $notification): bool {
                        return static::isActionable($notification)
                            && in_array($notification->data['record_model'] ?? null, [
                                BmhpStockOpname::class,
                                BmhpPurchase::class,
                                ProjectRequest::class,
                            ]);
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // --- LOGIKA INTI ---
    public static function isActionable(DatabaseNotification $notification): bool
    {
        // 1. Apakah notifikasi punya data 'is_approvable', 'record_model', dan 'record_id'?
        if (empty($notification->data['is_approvable']) || empty($notification->data['record_model']) || empty($notification->data['record_id'])) {
            return false; // <-- KEMUNGKINAN BESAR GAGAL DI SINI
        }

        // 2. Apakah record sumber (misal: ProjectRequest) ditemukan?
        $sourceRecord = ($notification->data['record_model'])::find($notification->data['record_id']);

        // 3. Apakah record sumber statusnya 'pending'?
        return $sourceRecord && $sourceRecord->status === 'pending';
    }


    public static function handleApproval(DatabaseNotification $notification, string $newStatus): void
    {
        $modelClass = $notification->data['record_model'];
        $recordId = $notification->data['record_id'];
        $sourceRecord = $modelClass::find($recordId);

        if (!$sourceRecord) {
            FilamentNotification::make()->warning()->title('Tidak Ditemukan')->body('Pengajuan tidak ditemukan.')->send();
            return;
        }

        // For ProjectRequest, handle two-level approval
        if (get_class($sourceRecord) === ProjectRequest::class) {
            $approvalLevel = $notification->data['approval_level'] ?? 1;
            $approver = auth()->user();

            // Check which level and update accordingly
            if ($approvalLevel === 1) {
                if ($sourceRecord->approval_level_1_status !== 'pending') {
                    FilamentNotification::make()->warning()->title('Sudah Diproses')->body('Persetujuan Level 1 sudah ditindaklanjuti.')->send();
                    return;
                }

                $sourceRecord->update([
                    'approval_level_1_status' => $newStatus,
                    'approval_level_1_by' => $approver->id,
                    'approval_level_1_at' => now(),
                ]);

                // If approved, set level 2 to pending and notify level 2 approvers
                if ($newStatus === 'approved') {
                    $sourceRecord->update(['approval_level_2_status' => 'pending']);

                    // Send notification to Level 2 approvers
                    $level2Approvers = User::permission('approve_project_level_2')->get();
                    foreach ($level2Approvers as $level2Approver) {
                        $level2Approver->notify(new \App\Notifications\ProjectRequestLevel2Approval($sourceRecord));
                    }
                }

                // If rejected, update overall status to rejected
                if ($newStatus === 'rejected') {
                    $sourceRecord->update(['status' => 'rejected']);
                }
            } elseif ($approvalLevel === 2) {
                if ($sourceRecord->approval_level_2_status !== 'pending') {
                    FilamentNotification::make()->warning()->title('Sudah Diproses')->body('Persetujuan Level 2 sudah ditindaklanjuti.')->send();
                    return;
                }

                $sourceRecord->update([
                    'approval_level_2_status' => $newStatus,
                    'approval_level_2_by' => $approver->id,
                    'approval_level_2_at' => now(),
                ]);

                // Update overall status based on level 2 decision
                $sourceRecord->update(['status' => $newStatus]);

                // If approved, update asset status
                if ($newStatus === 'approved') {
                    Aset::whereIn('id', $sourceRecord->asset_ids ?? [])->update(['status' => 'unavailable']);
                }
            }

            // Send feedback notification to creator
            if ($sourceRecord->user) {
                $approverName = $approver->name ?? 'Admin';

                if ($newStatus === 'approved') {
                    $sourceRecord->user->notify(new \App\Notifications\ProjectRequestApprovedNotification($sourceRecord, $approverName));
                } elseif ($newStatus === 'rejected') {
                    $sourceRecord->user->notify(new \App\Notifications\ProjectRequestRejectedNotification($sourceRecord, $approverName));
                }
            }
        } else {
            // Handle other models (BmhpStockOpname, etc.) - old logic
            if ($sourceRecord->status !== 'pending') {
                FilamentNotification::make()->warning()->title('Sudah Diproses')->body('Pengajuan ini sudah ditindaklanjuti.')->send();
                return;
            }

            DB::transaction(function () use ($sourceRecord, $newStatus) {
                if (get_class($sourceRecord) === BmhpStockOpname::class) {
                    if ($newStatus === 'approved') {
                        $sourceRecord->bmhp->update(['stok_sisa' => $sourceRecord->stok_fisik]);
                    }
                }

                if (get_class($sourceRecord) === BmhpPurchase::class) {
                    if ($newStatus === 'approved') {
                        $sourceRecord->loadMissing('items.bmhp');
                        foreach ($sourceRecord->items as $item) {
                            $pcs = (int) ($item->total_pcs ?? 0);
                            if ($pcs > 0) {
                                $item->bmhp->increment('stok_sisa', $pcs);
                            }
                        }

                        $sourceRecord->update([
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                    }
                }
                $sourceRecord->update(['status' => $newStatus]);
            });
        }

        $notification->markAsRead();
        FilamentNotification::make()->success()->title('Berhasil')->body("Pengajuan telah berhasil di-{$newStatus}.")->send();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'view' => Pages\ViewNotification::route('/{record}'),
        ];
    }
}
