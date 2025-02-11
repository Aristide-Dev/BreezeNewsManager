<?php

namespace AristechDev\NewsManager\Providers;

use Illuminate\Support\ServiceProvider;

class NewsManagerServiceProvider extends ServiceProvider
{
    /**
     * Méthode de démarrage du package.
     *
     * @return void
     */
    public function boot()
    {
        // Charger les différents fichiers de routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes/news.php');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes/media.php');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes/documents.php');

        // Charger les vues du package
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'newsmanager');

        // Publier la configuration, les migrations et les vues si l'application tourne en console
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/news.php' => config_path('news.php'),
            ], 'newsmanager-config');

            $this->publishes([
                __DIR__ . '/../../src/Database/migrations/' => database_path('migrations'),
            ], 'newsmanager-migrations');

            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/newsmanager'),
            ], 'newsmanager-views');
        }
    }

    /**
     * Enregistrer les services du package.
     *
     * @return void
     */
    public function register()
    {
        // Fusionner la configuration du package avec celle de l'application
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/news.php',
            'news'
        );
    }
} 