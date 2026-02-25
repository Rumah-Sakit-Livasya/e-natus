<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class BmhpCluster extends Cluster
{
    protected static ?string $slug = 'bhp';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Manajemen BHP';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        $permissions = [
            'view bmhp',
            'view stock opname',
        ];

        return $user->hasAnyPermission($permissions);
    }
}
