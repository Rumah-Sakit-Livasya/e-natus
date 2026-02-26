<?php

namespace App\Models;

use App\Notifications\NewProjectRequestNotification;
use App\Notifications\ProjectRequestCreated;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProjectRequest extends Model
{
    use SoftDeletes;
    protected $table = 'project_requests';
    protected $guarded = ['id'];

    protected $casts = [
        'employee_ids' => 'array',
        'sdm_ids' => 'array',
        'asset_ids' => 'array',
        'vendor_rental_ids' => 'array',
        'due_date' => 'date',
        'start_period' => 'date',
        'end_period' => 'date',
        'approval_level_1_at' => 'datetime',
        'approval_level_2_at' => 'datetime',
    ];

    protected static function booted()
    {
        // Event yang berjalan SEBELUM record disimpan
        static::creating(function ($projectRequest) {
            if (empty($projectRequest->user_id) && Auth::check()) {
                $projectRequest->user_id = Auth::id();
            }

            if (empty($projectRequest->code)) {
                $projectRequest->code = self::generateCode($projectRequest->name);
            }

            // Set initial approval status based on settings
            if (! \App\Models\GeneralSetting::isProjectL1Required()) {
                $projectRequest->approval_level_1_status = 'approved';
                $projectRequest->approval_level_1_at = now();
                $projectRequest->approval_level_1_by = Auth::id();
            } else {
                $projectRequest->approval_level_1_status = 'pending';
            }
        });

        // Event yang berjalan SETELAH record disimpan
        static::created(function ($projectRequest) {
            // Only send notification if this is a new request pending Level 1 approval
            if ($projectRequest->approval_level_1_status === 'pending') {
                // Kirim notifikasi ke user dengan permission 'approve_project_level_1'
                $users = User::permission('approve_project_level_1')->get();

                // Kirim notifikasi SEKARANG (ID sudah ada dan valid)
                foreach ($users as $user) {
                    try {
                        $user->notify(new ProjectRequestCreated($projectRequest));
                        \Log::info('Notification sent', [
                            'project_id' => $projectRequest->id,
                            'user_id' => $user->id,
                            'user_name' => $user->name
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send notification', [
                            'project_id' => $projectRequest->id,
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        });

        // Event yang berjalan SETELAH record diupdate
        static::updated(function ($projectRequest) {
            // Check if project just became fully approved
            if ($projectRequest->isFullyApproved() && !$projectRequest->bmhp_stock_deducted) {
                $projectRequest->deductBmhpStock();
                \Log::info('Stock automatically deducted on approval', [
                    'project_id' => $projectRequest->id,
                    'project_name' => $projectRequest->name
                ]);
            }
        });
    }

    public static function generateCode($projectName): string
    {
        $prefix = "MCU";
        $now = now();
        $bulanRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $bulan = $now->month;
        $tahun = $now->year;
        $romawi = $bulanRomawi[$bulan];
        $count = self::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->count() + 1;
        $urutan = str_pad($count, 3, '0', STR_PAD_LEFT);
        return "{$prefix}{$urutan}/{$romawi}/{$tahun}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getAssetsAttribute()
    {
        return \App\Models\Aset::whereIn('id', $this->asset_ids ?? [])->get();
    }

    // ===================================================================
    // HANYA ADA SATU DEFINISI sdm() SEKARANG
    // Definisi yang menggunakan `belongsToMany` telah dihapus.
    // ===================================================================
    public function assignedEmployees()
    {
        // Ambil array ID dari kolom sdm_ids
        $employeeIds = $this->sdm_ids ?? [];

        // Lakukan query ke model Employee (atau SDM) yang ID-nya ada di array tersebut.
        // Ganti \App\Models\Employee::class jika nama model Anda berbeda (misal: SDM::class)
        return \App\Models\Employee::whereIn('id', $employeeIds);
    }

    public function rencanaAnggaranBiaya(): HasMany
    {
        return $this->hasMany(RencanaAnggaranBiaya::class);
    }

    // public function realisationRabItems(): HasMany
    // {
    //     return $this->hasMany(\App\Models\RealisationRabItem::class);
    // }

    public function rabOperasionalItems(): HasMany
    {
        return $this->hasMany(RabOperasionalItem::class);
    }

    public function rabFeeItems(): HasMany
    {
        return $this->hasMany(RabFeeItem::class);
    }

    public function rabClosing(): HasOne
    {
        return $this->hasOne(RabClosing::class);
    }

    /**
     * Mendefinisikan relasi ke data Absensi yang sudah terjadi.
     * Ini adalah relasi one-to-many ke tabel project_attendances.
     */
    public function projectAttendances(): HasMany
    {
        return $this->hasMany(ProjectAttendance::class, 'project_request_id');
    }

    public function pengajuanDanas(): HasMany
    {
        return $this->hasMany(PengajuanDana::class);
    }

    /**
     * Sebuah Project Request memiliki banyak Participant.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function projectBmhp()
    {
        return $this->hasMany(ProjectBmhp::class);
    }

    public function bmhpClosingItems(): HasMany
    {
        return $this->hasManyThrough(
            RabClosingBmhpItem::class,
            RabClosing::class,
            'project_request_id', // FK di RabClosing
            'rab_closing_id',     // FK di RabClosingBmhpItem
            'id',
            'id'
        );
    }

    // =================== APPROVAL RELATIONSHIPS ===================

    /**
     * User who approved Level 1
     */
    public function approvalLevel1By()
    {
        return $this->belongsTo(User::class, 'approval_level_1_by');
    }

    /**
     * User who approved Level 2
     */
    public function approvalLevel2By()
    {
        return $this->belongsTo(User::class, 'approval_level_2_by');
    }

    // =================== APPROVAL HELPER METHODS ===================

    /**
     * Check if project is pending Level 1 approval
     */
    public function isPendingLevel1Approval(): bool
    {
        return $this->approval_level_1_status === 'pending';
    }

    /**
     * Check if project is pending Level 2 approval
     */
    public function isPendingLevel2Approval(): bool
    {
        return $this->approval_level_1_status === 'approved'
            && $this->approval_level_2_status === 'pending';
    }

    /**
     * Check if project is fully approved (both levels)
     */
    public function isFullyApproved(): bool
    {
        return $this->approval_level_1_status === 'approved'
            && $this->approval_level_2_status === 'approved';
    }

    /**
     * Check if any level has been rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_level_1_status === 'rejected'
            || $this->approval_level_2_status === 'rejected';
    }

    /**
     * Deduct BMHP stock and mark as deducted
     */
    public function deductBmhpStock()
    {
        if ($this->bmhp_stock_deducted) {
            return;
        }

        foreach ($this->projectBmhp as $item) {
            if ($item->bmhp) {
                // Decrement stok_sisa by total pieces (jumlah_rencana)
                $item->bmhp->decrement('stok_sisa', $item->jumlah_rencana);

                \Illuminate\Support\Facades\Log::info("Stock Deducted (Centralized): Project Request Approved for {$this->name}. BMHP {$item->bmhp->name} deducted {$item->jumlah_rencana} Pcs.");
            }
        }

        $this->update(['bmhp_stock_deducted' => true]);
    }

    /**
     * Mark used assets as unavailable
     */
    public function markAssetsUnavailable()
    {
        if (!empty($this->asset_ids)) {
            \App\Models\Aset::whereIn('id', $this->asset_ids)->update(['status' => 'unavailable']);
        }
    }
}
