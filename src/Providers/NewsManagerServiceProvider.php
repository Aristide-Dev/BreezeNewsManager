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
    public function boot(): void
    {
        // Publication de la configuration du package
        $this->publishes([
            __DIR__ . '/../../config/newsmanager.php' => config_path('newsmanager.php'),
        ], 'newsmanager-config');

        // Récupérer la stack sélectionnée (via config ou variable d'environnement)
        $stack = config('newsmanager.stack', 'blade');

        // Définir les dossiers pour les routes et les vues selon la stack.
        // Pour "vue", les routes se trouvent dans "VueJs" et les vues dans "vueJs" d'après votre arborescence.
        $routesFolder = $stack === 'vue' ? 'VueJs' : ucfirst($stack);
        $viewsFolder = $stack === 'vue' ? 'vueJs' : ucfirst($stack);

        // Charger toutes les routes de la stack sélectionnée
        foreach (glob(__DIR__ . '/../../routes/' . $routesFolder . '/*.php') as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }

        // Charger les vues de la stack sélectionnée
        $this->loadViewsFrom(__DIR__ . '/../../resources/' . $viewsFolder, 'newsmanager');

        // Charger les migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../src/Database/migrations');

        // Charger conditionnellement les ressources spécifiques à chaque module
        // Le fichier de configuration (config/newsmanager.php) doit retourner un tableau pour 'modules'
        $modules = config('newsmanager.modules', []); // Exemple : ['news', 'media', 'documents']

        // Exemple : charger les vues spécifiques pour le module "news" si le dossier existe
        if (in_array('news', $modules)) {
            $newsViewsPath = __DIR__ . '/../../resources/News';
            if (is_dir($newsViewsPath)) {
                $this->loadViewsFrom($newsViewsPath, 'newsmanager-news');
            }
        }
        if (in_array('media', $modules)) {
            $mediaViewsPath = __DIR__ . '/../../resources/Media';
            if (is_dir($mediaViewsPath)) {
                $this->loadViewsFrom($mediaViewsPath, 'newsmanager-media');
            }
        }
        if (in_array('documents', $modules)) {
            $documentsViewsPath = __DIR__ . '/../../resources/Documents';
            if (is_dir($documentsViewsPath)) {
                $this->loadViewsFrom($documentsViewsPath, 'newsmanager-documents');
            }
        }

        // Publication des ressources lorsque l'application tourne en console
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

            // Enregistrement des commandes artisan
            $this->commands([
                \AristechDev\NewsManager\Console\Commands\InstallNewsPackageBlade::class,
                \AristechDev\NewsManager\Console\Commands\InstallNewsPackageReact::class,
                \AristechDev\NewsManager\Console\Commands\InstallNewsPackageVue::class,
                \AristechDev\NewsManager\Console\Commands\InstallNewsModules::class,
            ]);
        }
    }

    /**
     * Enregistrer les services du package.
     *
     * @return void
     */
    public function register(): void
    {
        // Fusionner la configuration du package avec celle de l'application
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/newsmanager.php',
            'newsmanager'
        );
    }
}
