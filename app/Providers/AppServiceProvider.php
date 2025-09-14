<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el servicio de IA Editorial
        $this->app->singleton(\App\Services\AiEditorialService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar PostObserver para automatización con IA
        \App\Models\Post::observe(\App\Observers\PostObserver::class);

        // Compartir configuraciones del sitio a todas las vistas
        try {
            $site = cache()->remember('site_settings', 300, fn () => \App\Models\SiteSetting::first());
            \Illuminate\Support\Facades\View::share('site', $site);
        } catch (\Throwable $e) {
            // En primeras migraciones puede no existir la tabla, no pasa nada
        }
    }
}
