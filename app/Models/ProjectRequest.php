<?php

namespace App\Models;

use App\Notifications\NewProjectRequestNotification;
use App\Notifications\ProjectRequestCreated;
use App\Traits\HasNotifications;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProjectRequest extends Model
{
    use SoftDeletes, HasNotifications;
    protected $table = 'project_requests';
    protected $guarded = ['id'];

    protected $casts = [
        'employee_ids' => 'array',
        'sdm_ids' => 'array',
        'asset_ids' => 'array',
        'start_period' => 'date',
        'end_period' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($projectRequest) {
            if (Auth::check()) {
                $projectRequest->user_id = Auth::id();
            }

            $superadmins = User::role('super-admin')->get();

            foreach ($superadmins as $user) {
                $user->notify(new ProjectRequestCreated($projectRequest));
            }

            if (empty($projectRequest->code)) {
                $projectRequest->code = self::generateCode($projectRequest->name);
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
}
