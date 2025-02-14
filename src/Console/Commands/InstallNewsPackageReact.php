<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InstallNewsPackageReact extends Command
{
    /**
     * La signature de la commande.
     *
     * @var string
     */
    protected $signature = 'aristechnews:install:react';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Installation du package NewsManager pour la stack React (copie des controllers, routes et vues)';

    /**
     * Exécute la commande.
     */
    public function handle(): void
    {
        $this->info('=== Installation de NewsManager pour la stack React ===');

        // Étape 1 : Vérifier et installer Laravel Breeze si nécessaire
        $this->checkAndInstallBreeze();

        // Pour cet exemple, nous fixons la stack à "react".
        $stack = 'react';
        $this->info("Stack sélectionnée : " . $stack);

        // Étape 2 : Copier les Controllers (s'ils existent)
        $this->copyDirectoryIfExists(
            __DIR__ . '/../../../src/Http/Controllers/' . ucfirst($stack),
            app_path('Http/Controllers/'),
            'Controllers'
        );

        // Étape 3 : Copier les Routes
        $this->copyDirectoryIfExists(
            __DIR__ . '/../../../routes/' . ucfirst($stack),
            base_path('routes/'),
            'Routes'
        );

        // Étape 4 : Copier les Vues
        // Pour React, nous copions à la fois le dossier "Js" et "views" de React.
        $this->copyDirectoryIfExists(
            __DIR__ . '/../../../resources',
            resource_path('/'),
            'React Views'
        );
        // Vous pouvez également copier d'autres dossiers spécifiques (comme "Js")
        // $this->copyDirectoryIfExists(
        //     __DIR__ . '/../../resources/React/Js',
        //     resource_path('js/' . ucfirst($stack)),
        //     'Fichiers JS'
        // );

        // Étape 5 : Installer Laravel Breeze pour la stack React
        $this->info("Installation de Laravel Breeze pour React...");
        $this->call('breeze:install', [
            'stack' => $stack,
            '--no-interaction' => true,
        ]);

        $this->info('Installation de NewsManager pour React terminée.');
        $this->info('N’oubliez pas d’exécuter "php artisan migrate" et "npm install && npm run dev" pour finaliser la configuration.');
    }

    /**
     * Vérifie si Laravel Breeze est installé et l'installe si nécessaire.
     */
    protected function checkAndInstallBreeze(): void
    {
        if (!class_exists(\Laravel\Breeze\BreezeServiceProvider::class)) {
            $this->error('Laravel Breeze n\'est pas installé. Installation automatique en cours...');
            $process = new Process(['composer', 'require', 'laravel/breeze']);
            $process->setWorkingDirectory(base_path());
            $process->run(function ($type, $buffer) {
                $this->line($buffer);
            });
            if (!$process->isSuccessful()) {
                $this->error("L'installation de Laravel Breeze a échoué. Veuillez l'installer manuellement : composer require laravel/breeze");
                exit(1);
            }
            $this->info('Laravel Breeze a été installé avec succès.');
        }
    }

    /**
     * Copie un répertoire source vers un répertoire destination s'il existe.
     *
     * @param string $source
     * @param string $destination
     * @param string $label Label pour les messages affichés
     */
    protected function copyDirectoryIfExists(string $source, string $destination, string $label): void
    {
        if (File::exists($source)) {
            $this->info("Copie des {$label} depuis {$source} vers {$destination}...");
            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            if (File::copyDirectory($source, $destination)) {
                $this->info("Les {$label} ont été copiés avec succès.");
            } else {
                $this->error("La copie des {$label} a échoué.");
            }
        } else {
            $this->warn("Aucun dossier {$label} trouvé à copier depuis {$source}.");
        }
    }
}
