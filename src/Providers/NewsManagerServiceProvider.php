<?php

namespace AristechDev\NewsManager\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class NewsManagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Détection automatique de la stack
        $stack = $this->detectStack();

        // Définir les dossiers pour les routes et les vues selon la stack détectée
        $routesFolder = ucfirst($stack);
        $viewsFolder = $stack === 'vue' ? 'vueJs' : ucfirst($stack);

        // Charger les routes et les vues correspondantes
        foreach (glob(__DIR__ . '/../../routes/' . $routesFolder . '/*.php') as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }

        $this->loadViewsFrom(__DIR__ . '/../../resources/' . $viewsFolder, 'newsmanager');

        // Autres configurations...
    }

    public function register()
    {
        $this->commands([
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackageReact::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsModules::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackageBlade::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackageReact::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackageVue::class,
        ]);
    }

    private function detectStack(): string
    {
        if (File::exists(base_path('resources/views/welcome.blade.php'))) {
            return 'blade';
        }

        if (File::exists(base_path('resources/Js/Pages/Welcome.jsx'))) {
            return 'react';
        }

        if (File::exists(base_path('resources/Js/Pages/Welcome.tsx'))) {
            return 'react';
        }

        if (File::exists(base_path('resources/Js/Pages/Welcome.vue'))) {
            return 'vue';
        }

        if (File::exists(base_path('resources/js/app.js'))) {
            $appJsContent = File::get(base_path('resources/js/app.js'));
            if (strpos($appJsContent, 'import { createApp } from \'vue\'') !== false) {
                return 'vue';
            }
            if (strpos($appJsContent, 'import React from \'react\'') !== false) {
                return 'react';
            }
        }

        // Valeur par défaut si aucune stack n'est détectée
        return 'blade';
    }
}
