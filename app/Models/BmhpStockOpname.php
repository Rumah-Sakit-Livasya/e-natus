<?php

namespace App\Models;

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
        static::creating(function (BmhpStockOpname $stockOpname) {
            if (empty($stockOpname->user_id) && Auth::check()) {
                $stockOpname->user_id = Auth::id();
            }
        });

        static::created(function (BmhpStockOpname $stockOpname) {
            $stockOpname->loadMissing(['bmhp', 'user']);

            $users = User::role(['super-admin', 'owner'])->get();

            foreach ($users as $user) {
                $user->notify(new StockOpnameCreated($stockOpname));
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
