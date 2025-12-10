<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aset extends Model
{
    use SoftDeletes;

    protected $table = 'aset';

    protected $fillable = [
        'template_id',
        'supplier_id',
        'asset_receipt_item_id',
        'lander_id',
        'custom_name',
        'type',
        'serial_number',
        'code',
        'condition',
        'brand',
        'purchase_year',
        'tarif',
        'satuan',
        'index',
        'image',
        'status',
    ];


    public static function importableUsing(): array
    {
        return [
            'custom_name',
            'type',
            'serial_number',
            'code',
            'condition',
            'brand',
            'purchase_year',
            'tarif',
            'satuan',
            'status',
            'template_id',
            'lander_id',
            'receipt_id',
        ];
    }

    protected static function booted()
    {
        static::creating(function (Aset $model) {
            $model->index = self::generateNextIndex($model->template_id);

            if (empty($model->code)) {
                $model->code = self::generateUniqueCode($model);
                \Log::info('Generate kode otomatis: ' . $model->code);
            }
        });

        static::updating(function (Aset $model) {
            if ($model->isDirty('template_id')) {
                $model->index = self::generateNextIndex($model->template_id);
            }

            if (
                empty($model->code) ||
                $model->isDirty('lander_id') ||
                $model->isDirty('template_id')
            ) {
                $model->loadMissing(['lander', 'template.category']);
                $model->code = self::generateUniqueCode($model);
            }
        });
    }

    // ✅ RELASI BARU
    public function receipt()
    {
        return $this->belongsTo(AsetReceipt::class, 'receipt_id');
    }

    public function template()
    {
        return $this->belongsTo(\App\Models\Template::class);
    }


    public function lander()
    {
        return $this->belongsTo(Lander::class);
    }

    public function projectRequests()
    {
        // Pastikan pencarian juga cocok dengan string id (misal: "1" bukan 1)
        return \App\Models\ProjectRequest::query()
            ->whereJsonContains('asset_ids', (string) $this->id);
    }

    public function getProjectRequestsAttribute()
    {
        return \App\Models\ProjectRequest::whereJsonContains('asset_ids', (string) $this->id)->get();
    }

    protected static function generateUniqueCode(Aset $model): string
    {
        $landerCode   = $model->lander?->code ?? 'LDR';
        $categoryCode = $model->template?->category?->code ?? 'CAT';
        $templateCode = $model->template?->code ?? 'TMP';

        $lastCode = static::where('code', 'like', "{$landerCode}/{$categoryCode}/{$templateCode}/%")
            ->latest('id')
            ->value('code');

        $lastNumber = 0;
        if ($lastCode && preg_match('/\/(\d+)$/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "{$landerCode}/{$categoryCode}/{$templateCode}/{$nextNumber}";
    }

    protected static function generateNextIndex(?int $templateId): int
    {
        if (! $templateId) {
            return 1;
        }

        $lastIndex = static::where('template_id', $templateId)
            ->max('index');

        return ($lastIndex !== null ? $lastIndex + 1 : 1);
    }

    public function safeSetAttribute($key, $value)
    {
        if (!in_array($key, $this->fillable)) {
            return $this;
        }
        return parent::setAttribute($key, $value);
    }
}
