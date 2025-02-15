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

        // Installer les modules via la commande dédiée
        $this->info('Installation des modules...');
        $this->call('aristechnews:install:modules', [
            '--stack' => 'react'
        ]);

        // Installation des dépendances spécifiques à la stack React
        $this->installDependencies();

        // Copier les fichiers de base React
        // $this->copyBaseReactFiles();

        // Compiler les assets front-end
        $this->compileAssets();

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
            '@lexical/react',
            '@tailwindcss/forms',
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
        
        // Appel de la commande Breeze pour installer le stack choisi
        $this->call('breeze:install', [
            'stack' => 'react',
            '--no-interaction' => true,
        ]);
    }

    /**
     * Copie les fichiers de base pour React
     */
    protected function copyBaseReactFiles(): void
    {
        $sourceViews = __DIR__ . '/../../resources/React';
        $destinationViews = resource_path('/');

        if (!File::isDirectory($destinationViews)) {
            File::makeDirectory($destinationViews, 0755, true);
        }

        // Copier les fichiers de base React
        $this->copyDirectoryIfExists(
            __DIR__ . '/../../../resources/React',
            resource_path('/'),
            'React Views de base'
        );
    }

    /**
     * Compile les assets front-end
     */
    protected function compileAssets(): void
    {
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
