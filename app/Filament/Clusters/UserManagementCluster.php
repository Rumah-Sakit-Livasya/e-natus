<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class UserManagementCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'User Management';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        $permissions = [
            'view users',
            'view roles',
            'view permissions',
        ];

        return $user->hasAnyPermission($permissions);
    }
}
