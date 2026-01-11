<?php

namespace App\Providers;

use App\Filament\Resources\ProjectRequestResource;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function getResources(): array
    {
        return [
            ProjectRequestResource::class,
            // resource lain...
        ];
    }
}
