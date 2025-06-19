<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsetReceipt extends Model
{
    use SoftDeletes;
    protected $table = 'aset_receipts';
    protected $fillable = ['procurement_id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receiptItems()
    {
        return $this->hasMany(AssetReceiptItem::class, 'aset_receipt_id');
    }

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }
}
