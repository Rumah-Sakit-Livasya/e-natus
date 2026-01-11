<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetReceiptItem extends Model
{
    use SoftDeletes;
    protected $table = 'asset_receipt_items';

    protected $fillable = [
        'aset_receipt_id',
        'template_id',
        'lander_id',
        'custom_name',
        'code',
        'condition',
        'brand',
        'purchase_year',
        'tarif',
        'satuan',
        'quantity',
    ];

    protected static function booted()
    {
        static::creating(function (AssetReceiptItem $model) {
            if (empty($model->code)) {
                $model->code = self::generateReceiptCode($model);
                \Log::info('Generate kode otomatis: ' . $model->code);
            }
        });

        static::updating(function (AssetReceiptItem $model) {
            if (
                empty($model->code) ||
                $model->isDirty('lander_id') ||
                $model->isDirty('template_id')
            ) {
                $model->loadMissing(['lander', 'template.category']);
                $model->code = self::generateReceiptCode($model);
            }
        });
    }

    public static function generateReceiptCode(): string
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        // Format bulan Romawi
        $romanMonths = [
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
        $monthRoman = $romanMonths[$month];

        // Hitung jumlah penerimaan di bulan & tahun ini
        $count = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return "REC/{$nextNumber}/{$monthRoman}/{$year}";
    }


    public function receipt()
    {
        return $this->belongsTo(AsetReceipt::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function lander()
    {
        return $this->belongsTo(Lander::class);
    }
}
