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

        $this->loadViewsFrom(__DIR__ . '/../../resources/' . $viewsFolder, '');

        // Autres configurations...
    }

    public function register()
    {
        $this->commands([
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackage::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsModules::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackageBlade::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackageReact::class,
            \AristechDev\NewsManager\Console\Commands\InstallNewsPackageVue::class,
        ]);
    }

    private function detectStack(): string
    {
        $stack = ''; 
        if (File::exists(base_path('resources/views/welcome.blade.php'))) {
            $stack = 'blade';
        }

        if (File::exists(base_path('resources/Js/Pages/Welcome.jsx'))) {
            $stack = 'react';
        }

        if (File::exists(base_path('resources/Js/Pages/Welcome.tsx'))) {
            $stack = 'react';
        }

        if (File::exists(base_path('resources/Js/Pages/Welcome.vue'))) {
            $stack = 'vue';
        }

        if (File::exists(base_path('resources/js/app.js'))) {
            $appJsContent = File::get(base_path('resources/js/app.js'));
            if (strpos($appJsContent, 'import { createApp } from \'vue\'') !== false) {
                $stack = 'vue';
            }
            if (strpos($appJsContent, 'import React from \'react\'') !== false) {
                $stack = 'react';
            }
        }


        if ($stack === 'blade' || $stack === 'react' || $stack === 'vue') {
            return $stack;
        }

        // Fallback if no valid stack is detected
        \Log::warning('No valid frontend stack detected. Defaulting to React.');
        return 'React'; // or handle more gracefully
    }
}
