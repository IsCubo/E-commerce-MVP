<?php

namespace App\Models;

use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Todos los settings (key => value), leídos del mismo caché que usa
     * AppServiceProvider para compartirlos con las vistas. Úsalo en vez de
     * consultar Setting::where(...) directo — evita golpear la BD en cada
     * acceso (p. ej. desde accessors de modelos como whatsapp_url, que se
     * evalúan una vez por cada producto/combo renderizado).
     */
    public static function cached(): \Illuminate\Support\Collection
    {
        $cached = Cache::get(AppServiceProvider::SETTINGS_CACHE_KEY);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $settings = static::all()->pluck('value', 'key');
        } catch (\Throwable) {
            // Tabla "settings" todavía no existe (p. ej. instalación fresca
            // antes de correr migraciones). No cacheamos este resultado
            // vacío para que el próximo request reintente la consulta real
            // en cuanto la tabla ya exista, en vez de quedar vacío "para
            // siempre" con rememberForever.
            return collect();
        }

        Cache::forever(AppServiceProvider::SETTINGS_CACHE_KEY, $settings);

        return $settings;
    }
}
