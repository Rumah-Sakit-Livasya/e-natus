<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class MasterDataCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Master Data';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        $permissions = [
            'view clients',
            'view regions',
            'view templates',
            'view supplier',
        ];

        return $user->hasAnyPermission($permissions);
    }
}
