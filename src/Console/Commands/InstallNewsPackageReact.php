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
    protected $description = 'Commande d\'installation du package NewsManager pour la stack React';

    /**
     * Exécute la commande.
     */
    public function handle(): void
    {
        $this->info('Installation du package NewsManager pour la stack React.');

        // Vérifier et installer Laravel Breeze s'il n'est pas présent
        if (!class_exists(\Laravel\Breeze\BreezeServiceProvider::class)) {
            $this->error('Laravel Breeze n\'est pas installé. Installation automatique en cours...');
            $process = new Process(['composer', 'require', 'laravel/breeze']);
            $process->setWorkingDirectory(base_path());
            $process->run(function ($type, $buffer) {
                $this->line($buffer);
            });
            if (!$process->isSuccessful()) {
                $this->error("L'installation de Laravel Breeze a échoué. Veuillez l'installer manuellement : composer require laravel/breeze");
                return;
            }
            $this->info('Laravel Breeze a été installé avec succès.');
        }

        // Copier le répertoire de vues pour React depuis le package vers l'application Laravel
        $sourceViews      = __DIR__ . '/../../resources/React/views';
        $destinationViews = resource_path('/');

        

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
            __DIR__ . '/../../../resources/' . ucfirst($stack),
            resource_path('/'),
            'React Views'
        );

        if (!File::isDirectory($destinationViews)) {
            File::makeDirectory($destinationViews, 0755, true);
        }

        if (File::copyDirectory($sourceViews, $destinationViews)) {
            $this->info('Les vues React ont été copiées dans ' . $destinationViews);
        } else {
            $this->error('La copie des vues React a échoué.');
        }

        // Installation des dépendances spécifiques à la stack React
        $this->installDependencies();

        // Appeler la commande Breeze pour installer le stack React
        $this->call('breeze:install', ['stack' => 'react']);

        // Compiler les assets front-end
        $this->info("Compilation des assets front-end pour React...");
        $process = new Process(['npm', 'run', 'build']);
        $process->setWorkingDirectory(base_path());
        $process->run(function ($type, $buffer) {
            $this->line($buffer);
        });
        if (!$process->isSuccessful()) {
            $this->error("La compilation des assets front-end a échoué.");
            return;
        }
        $this->info('Compilation des assets front-end terminée.');

        $this->info('Installation du package NewsManager pour React terminée.');
    }

    /**
     * Installe les dépendances NPM spécifiques à la stack React.
     */
    protected function installDependencies(): void
    {
        $dependencies = [
            'lucide-react',
            'react-file-icon',
            '@lexical/react'
        ];

        foreach ($dependencies as $dependency) {
            $this->info("Installation de la dépendance {$dependency}...");
            $process = new Process(['npm', 'install', $dependency]);
            $process->setWorkingDirectory(base_path());
            $process->run(function ($type, $buffer) {
                $this->line($buffer);
            });
            if (!$process->isSuccessful()) {
                $this->error("L'installation de {$dependency} a échoué.");
                return;
            } else {
                $this->info("{$dependency} a été installé avec succès.");
            }
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
