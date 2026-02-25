<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class BmhpCluster extends Cluster
{
    protected static ?string $slug = 'bhp';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Manajemen BHP';
}
