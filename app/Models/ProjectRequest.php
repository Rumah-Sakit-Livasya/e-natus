<?php

namespace App\Models;

use App\Notifications\NewProjectRequestNotification;
use App\Notifications\ProjectRequestCreated;
use App\Traits\HasNotifications;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProjectRequest extends Model
{
    use SoftDeletes, HasNotifications;
    protected $table = 'project_requests';
    protected $guarded = ['id'];

    protected $casts = [
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

            $superadmins = User::role('super-admin')->get(); // pakai spatie/laravel-permission

            // Kirim notifikasi
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
        $prefix = "MCU"; // contoh: "mcu" jadi "MCU"
        $now = now();
        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        $bulan = $now->month;
        $tahun = $now->year;
        $romawi = $bulanRomawi[$bulan];

        $count = self::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count() + 1;

        $urutan = str_pad($count, 3, '0', STR_PAD_LEFT); // 001, 002, ...
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

    public function sdm()
    {
        return $this->belongsTo(SDM::class);
    }


    public function getAssetsAttribute()
    {
        return \App\Models\Aset::whereIn('id', $this->asset_ids ?? [])->get();
    }
}
