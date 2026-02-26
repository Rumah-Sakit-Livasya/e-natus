<?php

namespace App\Filament\Pages;

use App\Models\GeneralSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class SystemSetting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.system-setting';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'System Settings';
    protected static ?string $title = 'System Settings';
    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'project_request_level_1_required' => (bool) GeneralSetting::getSetting('project_request_level_1_required', false),
            'project_request_level_2_required' => (bool) GeneralSetting::getSetting('project_request_level_2_required', false),
            'bmhp_purchase_approval_required' => (bool) GeneralSetting::getSetting('bmhp_purchase_approval_required', true),
            'pengajuan_dana_approval_required' => (bool) GeneralSetting::getSetting('pengajuan_dana_approval_required', true),
            'price_change_approval_required' => (bool) GeneralSetting::getSetting('price_change_approval_required', true),
            'attendance_submission_approval_required' => (bool) GeneralSetting::getSetting('attendance_submission_approval_required', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Project Workflow')
                    ->description('Konfigurasi alur approval permintaan proyek.')
                    ->schema([
                        Toggle::make('project_request_level_1_required')
                            ->label('Wajib Approval Level 1 (Manager)')
                            ->helperText('Jika aktif, project request harus disetujui Level 1 sebelum lanjut ke Level 2 atau Approved.')
                            ->default(false),
                        Toggle::make('project_request_level_2_required')
                            ->label('Wajib Approval Level 2 (Finance/Owner)')
                            ->helperText('Jika aktif, project request harus disetujui Level 2 (Final) sebelum menjadi Approved.')
                            ->default(false),
                    ])->columns(2),

                Section::make('Inventory & Finance Workflow')
                    ->description('Konfigurasi alur approval untuk modul lainnya.')
                    ->schema([
                        Toggle::make('bmhp_purchase_approval_required')
                            ->label('Wajib Approval Pembelian BMHP')
                            ->default(true),
                        Toggle::make('pengajuan_dana_approval_required')
                            ->label('Wajib Approval Pengajuan Dana')
                            ->default(true),
                        Toggle::make('price_change_approval_required')
                            ->label('Wajib Approval Perubahan Harga')
                            ->default(true),
                        Toggle::make('attendance_submission_approval_required')
                            ->label('Wajib Approval Absensi')
                            ->helperText('Jika nonaktif, absensi otomatis disetujui setelah diinput.')
                            ->default(true),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ? '1' : '0']
            );
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return false;
    }
}
