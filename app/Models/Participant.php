<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = ['date_of_birth' => 'date'];

    public function mcuResults(): HasMany
    {
        return $this->hasMany(McuResult::class);
    }

    /**
     * Seorang Participant milik sebuah Project Request.
     */
    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function audiometryChecks(): HasMany
    {
        return $this->hasMany(AudiometryCheck::class);
    }

    public function drugTests(): HasMany
    {
        return $this->hasMany(DrugTest::class);
    }

    public function ekgChecks(): HasMany
    {
        return $this->hasMany(EkgCheck::class);
    }

    public function labChecks(): HasMany
    {
        return $this->hasMany(LabCheck::class);
    }

    public function rontgenChecks(): HasMany
    {
        return $this->hasMany(RontgenCheck::class);
    }

    public function spirometryChecks(): HasMany
    {
        return $this->hasMany(SpirometryCheck::class);
    }

    public function treadmillChecks(): HasMany
    {
        return $this->hasMany(TreadmillCheck::class);
    }

    public function usgAbdomenChecks(): HasMany
    {
        return $this->hasMany(UsgAbdomenCheck::class);
    }

    public function usgMammaeChecks(): HasMany
    {
        return $this->hasMany(UsgMammaeCheck::class);
    }
}
