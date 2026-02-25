<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ProjectCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'Project';
}
