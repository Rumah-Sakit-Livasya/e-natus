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
            'view asets',
            'view aset master',
            'view categories',
            'view aset categories',
            'view landers',
            'view aset landers',
            'view procurement',
            'view aset procurement',
            'view realisations',
            'view aset realisations',
            'view aset receipt',
            'view aset receipts',
            'view vendor rentals',
            'view aset vendor rentals',
            'view templates',
            'view aset templates',
        ];

        return $user->hasAnyPermission($permissions);
    }
}
