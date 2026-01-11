<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    protected $table = 'suppliers';
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    public function aset_receipts()
    {
        return $this->hasMany(\App\Models\AsetReceipt::class);
    }
}
