<?php

namespace App\Models;

use App\Notifications\StockOpnameCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class BmhpStockOpname extends Model
{
    protected $table = 'bmhp_stock_opnames';
    protected $fillable = ['bmhp_id', 'stok_fisik', 'keterangan'];

    protected static function booted()
    {
        static::created(function ($bmhpStockOpname) {
            // Kirim notifikasi ke user dengan role super-admin dan owner
            $users = \App\Models\User::role(['super-admin', 'owner'])->get();

            foreach ($users as $user) {
                $user->notify(new StockOpnameCreated($bmhpStockOpname));
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
