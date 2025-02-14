<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallNewsPackage extends Command
{
    /**
     * La signature de la commande.
     *
     * @var string
     */
    protected $signature = 'aristechnews:install {--stack= : La stack frontale à utiliser pour Breeze} {--modules= : Modules à installer (séparés par une virgule, ou "all" pour tout installer)}';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Commande interactive pour installer NewsManager, configurer Laravel Breeze et choisir les modules à installer';

    /**
     * Exécute la commande.
     */
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

        // Appel de la commande Breeze pour installer le stack choisi
        $this->call('breeze:install', [
            'stack' => $stack,
            '--no-interaction' => true,
        ]);

        // Déclenchement de la sous-commande spécifique en fonction du stack
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
                $this->info("Lancement de l'installation spécifique pour la stack VueJS...");
                $this->call('news:install:vue');
                break;
            default:
                $this->warn("Aucune sous-commande définie pour la stack {$stack}.");
                break;
        }

        // Optionnel : sélection des modules (news, media, documents)
        // ... (ici vous pouvez ajouter la logique de sélection des modules si souhaité)

        $this->info("Installation du package NewsManager terminée.");
        $this->info("N'oubliez pas d'exécuter 'php artisan migrate' et 'npm install && npm run dev' pour finaliser l'installation.");
    }

    /**
     * Vérifie si Laravel Breeze est installé et l'installe si nécessaire.
     */
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
