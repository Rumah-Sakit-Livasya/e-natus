<?php

namespace App\Providers;

use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
<<<<<<< HEAD
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Config;

=======
>>>>>>> 41a31ad1a8a01d6fb3f70df969516c7d431da7ea

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
	date_default_timezone_set(Config::get('app.timezone'));
   	Carbon::setLocale('id');
    	Carbon::setTestNow(); // reset kalau ada testing
    	Carbon::macro('inWIB', function () {
        	return $this->timezone('Asia/Jakarta');
    	});
=======
>>>>>>> 41a31ad1a8a01d6fb3f70df969516c7d431da7ea
        Filament::registerResources([
            UserResource::class,
            RoleResource::class,
            PermissionResource::class,
        ]);

        // Hanya aktifkan logging jika kita dalam mode debug untuk menghindari
        // memenuhi file log di production.
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        }
    }
}
