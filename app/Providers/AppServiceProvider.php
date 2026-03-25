<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(Registered::class, \App\Listeners\NotifyAdminsNewUserRegistered::class);

        // Rutas firmadas (ej. /entrar/{user}) usan la URL base de APP_URL. En XAMPP con
        // subcarpeta (/CRMnv/public) APP_URL debe coincidir con el navegador.
        $root = config('app.url');
        if (is_string($root) && $root !== '') {
            URL::forceRootUrl(rtrim($root, '/'));
        }
    }
}
