<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ProjectCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'Project';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        $permissions = [
            'view projects',
            'view rab awal',
            'view rab closing',
            'view pengajuan dana',
            'view request attendance project',
            'view attendance project',
            'view laporan margin',
            'view laporan pemeriksaan',
        ];

        return $user->hasAnyPermission($permissions);
    }
}
