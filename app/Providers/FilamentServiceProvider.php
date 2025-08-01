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
        // HAPUS ATAU KOMENTARI BLOK INI
        /*
        Filament::registerRenderHook(
            'panels::topbar.end',
            fn(): string => view('vendor.filament.components.topbar.notifications')->render(),
        );
        */
    }

    protected function getResources(): array
    {
        return [
            ProjectRequestResource::class,
            // resource lain...
        ];
    }
}
