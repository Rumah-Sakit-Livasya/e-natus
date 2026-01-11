<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusPengajuanEnum: string implements HasLabel, HasColor, HasIcon
{
    case DIAJUKAN = 'diajukan';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case DICAIRKAN = 'dicairkan';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DIAJUKAN => 'Diajukan',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::DICAIRKAN => 'Dicairkan',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::DIAJUKAN => 'warning',
            self::DISETUJUI => 'success',
            self::DITOLAK => 'danger',
            self::DICAIRKAN => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DIAJUKAN => 'heroicon-o-clock',
            self::DISETUJUI => 'heroicon-o-check-circle',
            self::DITOLAK => 'heroicon-o-x-circle',
            self::DICAIRKAN => 'heroicon-o-receipt-refund',
        };
    }
}
