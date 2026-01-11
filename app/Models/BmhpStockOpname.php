<?php

namespace App\Models;

use App\Notifications\ProjectRequestCreated;
use App\Notifications\StockOpnameCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class BmhpStockOpname extends Model
{
    protected $table = 'bmhp_stock_opnames';
    protected $fillable = ['bmhp_id', 'stok_fisik', 'keterangan', 'user_id', 'status'];

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
        });

        // Event yang berjalan SETELAH record disimpan
        static::created(function ($projectRequest) {
            // Ambil user dengan role super-admin dan owner
            $users = User::role(['super-admin', 'owner'])->get();

            // Kirim notifikasi SEKARANG (ID sudah ada dan valid)
            foreach ($users as $user) {
                $user->notify(new ProjectRequestCreated($projectRequest));
            }
        });
    }

    public function bmhp(): BelongsTo
    {
        return $this->belongsTo(Bmhp::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
