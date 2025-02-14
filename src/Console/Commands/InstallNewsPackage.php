<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallNewsPackage extends Command
{
    protected $signature = 'aristechnews:breeze:news {--stack= : La stack frontale à utiliser pour Breeze} {--modules= : Modules à installer (séparés par une virgule, ou "all" pour tout installer)}';
    protected $description = 'Commande interactive pour installer NewsManager, configurer Laravel Breeze et choisir les modules à installer';

    public function handle(): void
    {
        $this->info("=== Installation du package NewsManager ===");

        // Vérification et installation de Laravel Breeze
        $this->checkAndInstallBreeze();

        // Choix de la stack frontale
        $stack = $this->option('stack');
        if (!$stack) {
            $stack = $this->choice(
                'Quelle stack frontale souhaitez-vous installer pour Laravel Breeze ?',
                ['blade', 'react', 'vue'],
                1
            );
        }
        $this->info("Stack sélectionnée : " . $stack);

        // Appel de la commande Breeze pour installer le stack choisi
        $this->call('breeze:install', [
            'stack' => $stack,
            '--no-interaction' => true,
        ]);

        // Déclenchement de la sous-commande spécifique en fonction du stack
        switch ($stack) {
            case 'react':
                $this->info("Lancement de l'installation spécifique pour la stack React...");
                $this->call('aristechnews:install:react');
                break;
            case 'blade':
                $this->info("Lancement de l'installation spécifique pour la stack Blade...");
                $this->call('aristechnews:install:blade');
                break;
            case 'vue':
                $this->info("Lancement de l'installation spécifique pour la stack VueJS...");
                $this->call('aristechnews:install:vue');
                break;
            default:
                $this->warn("Aucune sous-commande définie pour la stack {$stack}.");
                break;
        }

        // Installation de la dépendance lucide-react via NPM
        $this->info("Installation de la dépendance lucide-react...");
        $process = new Process(['npm', 'install', 'lucide-react']);
        $process->setWorkingDirectory(base_path());
        $process->run(function ($type, $buffer) {
            $this->line($buffer);
        });
        if (!$process->isSuccessful()) {
            $this->error("L'installation de lucide-react a échoué.");
            return;
        }
        $this->info('lucide-react a été installé avec succès.');

        // Compilation des assets front-end
        $this->info("Compilation des assets front-end...");
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

        $this->info("Installation du package NewsManager terminée.");
        $this->info("N'oubliez pas d'exécuter 'php artisan migrate' pour finaliser l'installation.");
    }

    protected function checkAndInstallBreeze(): void
    {
        if (!class_exists(\Laravel\Breeze\BreezeServiceProvider::class)) {
            $this->error("Laravel Breeze n'est pas installé. Installation automatique en cours...");
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
    }
}
