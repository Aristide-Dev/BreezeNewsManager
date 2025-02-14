<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallNewsPackage extends Command
{
    protected $signature = 'news:install {--stack= : La stack frontale à utiliser pour Breeze} {--modules= : Modules à installer (séparés par une virgule, ou "all" pour tout installer)}';
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
                0
            );
        }
        $this->info("Stack sélectionnée : " . $stack);

        // Installation de Breeze avec la stack choisie
        $this->call('breeze:install', [
            'stack' => $stack,
            '--no-interaction' => true,
        ]);

        // Appel de la sous-commande spécifique selon la stack
        switch ($stack) {
            case 'react':
                $this->info("Lancement de l'installation spécifique pour la stack React...");
                $this->call('news:install:react');
                break;
            case 'blade':
                $this->info("Lancement de l'installation spécifique pour la stack Blade...");
                $this->call('news:install:blade');
                break;
            case 'vue':
                $this->info("Lancement de l'installation spécifique pour la stack Vue...");
                $this->call('news:install:vue');
                break;
            default:
                $this->warn("Aucune sous-commande définie pour la stack {$stack}.");
                break;
        }

        // Ici, vous pouvez ajouter la logique pour la sélection des modules si besoin

        $this->info("Installation du package NewsManager terminée.");
        $this->info("N'oubliez pas d'exécuter 'php artisan migrate' et 'npm install && npm run dev' pour finaliser l'installation.");
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
