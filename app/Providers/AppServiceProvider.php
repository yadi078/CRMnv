<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

        View::composer(['layouts.app', 'layouts.app-user'], function ($view) {
            $contact = null;
            $executiveUser = null;
            $route = request()->route();
            if ($route !== null) {
                $name = $route->getName();
                if ($name === 'contacts.show') {
                    $c = $route->parameter('contact');
                    if ($c instanceof Contact) {
                        $contact = $c;
                    }
                }
                if ($name === 'executives.show') {
                    $u = $route->parameter('user');
                    if ($u instanceof User) {
                        $executiveUser = $u;
                    }
                }
            }
            $view->with('globalReminderContact', $contact);
            $view->with('globalReminderExecutive', $executiveUser);
        });
    }
}
