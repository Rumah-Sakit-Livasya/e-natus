<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class AsetCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Manajemen Aset';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        $permissions = [
            'view aset',
            'view procurement',
            'view categories',
            'view landers',
            'view realisations',
            'view receipts',
            'view vendor rentals',
            'view templates',
        ];

        return $user->hasAnyPermission($permissions);
    }
}
