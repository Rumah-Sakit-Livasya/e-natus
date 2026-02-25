<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class UserManagementCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'User Management';
}
