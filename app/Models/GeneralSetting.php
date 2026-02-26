<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $table = 'general_settings';
    protected $fillable = ['key', 'value'];

    public static function getSetting(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function isProjectL1Required(): bool
    {
        return (bool) self::getSetting('project_request_level_1_required', false);
    }

    public static function isProjectL2Required(): bool
    {
        return (bool) self::getSetting('project_request_level_2_required', false);
    }

    public static function isBmhpPurchaseApprovalRequired(): bool
    {
        return (bool) self::getSetting('bmhp_purchase_approval_required', true);
    }

    public static function isPengajuanDanaApprovalRequired(): bool
    {
        return (bool) self::getSetting('pengajuan_dana_approval_required', true);
    }

    public static function isPriceChangeApprovalRequired(): bool
    {
        return (bool) self::getSetting('price_change_approval_required', true);
    }

    public static function isAttendanceApprovalRequired(): bool
    {
        return (bool) self::getSetting('attendance_submission_approval_required', true);
    }
}
