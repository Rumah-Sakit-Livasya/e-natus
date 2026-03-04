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
            'view aset master',
            'view aset categories',
            'view aset landers',
            'view aset procurement',
            'view aset realisations',
            'view aset receipts',
            'view aset vendor rentals',
            'view aset templates',
        ];

        return $user->hasAnyPermission($permissions);
    }
}
