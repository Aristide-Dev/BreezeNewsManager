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
        $this->info('=== Installation du package NewsManager pour la stack React ===');

        // Étape 1 : Vérifier et installer Laravel Breeze s'il n'est pas déjà installé
        $this->checkAndInstallBreeze();

        // Pour cet exemple, nous fixons la stack à "react"
        $stack = 'react';
        $this->info("Stack sélectionnée : " . $stack);

        // Étape 2 : Installer les modules via la commande dédiée
        $this->info('Installation des modules...');
        $this->call('aristechnews:install:modules', [
            '--stack' => $stack
        ]);

        // Étape 3 : Copier les fichiers de base pour React
        $this->copyBaseReactFiles();

        // Étape 4 : Installer les dépendances spécifiques à la stack React
        $this->installDependencies();

        // Étape 5 : Compiler les assets front-end
        $this->compileAssets();

        $this->info('Installation du package NewsManager pour React terminée.');
        $this->info('N’oubliez pas d’exécuter "php artisan migrate" pour finaliser la configuration.');
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
        } else {
            $this->info('Laravel Breeze est déjà installé.');
        }
    }

    /**
     * Copie les fichiers de base pour React depuis le package vers le répertoire de l'application.
     */
    protected function copyBaseReactFiles(): void
    {
        $sourcePath = __DIR__ . '/../../../resources/React';
        $destinationPath = resource_path('/');

        $this->info("Copie des fichiers de base React depuis {$sourcePath} vers {$destinationPath}...");
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $this->copyDirectoryIfExists($sourcePath, $destinationPath, 'React Views de base');
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
            }
            $this->info("{$dependency} a été installé avec succès.");
        }
    }

    /**
     * Compile les assets front-end.
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
     * @param string $source      Le chemin source dans le package
     * @param string $destination Le chemin destination dans l'application hôte
     * @param string $label       Label pour les messages affichés
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
