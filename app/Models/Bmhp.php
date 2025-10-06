<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bmhp extends Model
{
    use SoftDeletes;

    protected $table = 'bmhp';

    protected $fillable = [
        'name',
        'satuan',
        'stok_awal',
        'stok_sisa',
        'harga_satuan',
        'min_stok',
    ];

    // Ubah relasi ke project bmhp menjadi hasMany ke ProjectBmhp
    public function projectBmhp()
    {
        return $this->hasMany(ProjectBmhp::class, 'bmhp_id');
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(BmhpStockOpname::class);
    }
}
