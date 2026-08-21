<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Clave de caché para los settings globales. Pública para que
     * SettingController (y Setting::cached()) puedan invalidarla/leerla.
     */
    public const SETTINGS_CACHE_KEY = 'global_settings';

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
        // Share settings with all views. Setting::cached() ya maneja el
        // caso de que la tabla "settings" no exista todavía (instalación
        // fresca) y cachea el resultado — este composer corre en cada
        // vista renderizada, así que sin caché sería una consulta a
        // "settings" en cada vista, no solo en cada request.
        View::composer('*', function ($view) {
            $view->with('globalSettings', Setting::cached());
        });
    }
}
