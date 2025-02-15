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
        // Afficher un en-tête stylisé
        $this->displayHeader();

        $this->info("=== Installation du package NewsManager ===");

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

        // Déclenchement de la sous-commande spécifique en fonction de la stack choisie
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

        $this->info("Installation du package NewsManager terminée.");
        $this->info("N'oubliez pas d'exécuter 'php artisan migrate' pour finaliser l'installation.");
    }

    /**
     * Affiche un en-tête stylisé dans la console.
     */
    protected function displayHeader(): void
    {
        $header = <<<EOT
        <bg=white;fg=black>
         _______   _______   ________   ______   __       __  ________  _______         
        /       \ /       \ /        | /      \ /  \     /  |/        |/       \
        $$$$$$$  |$$$$$$$  |$$$$$$$$/ /$$$$$$  |$$  \   /$$ |$$$$$$$$/ $$$$$$$  |
        $$ |  $$ |$$ |__$$ |$$ |__    $$ |__$$ |$$$  \ /$$$ |$$ |__    $$ |__$$ |
        $$ |  $$ |$$    $$< $$    |   $$    $$ |$$$$  /$$$$ |$$    |   $$    $$<
        $$ |  $$ |$$$$$$$  |$$$$$/    $$$$$$$$ |$$ $$ $$/$$ |$$$$$/    $$$$$$$  |
        $$ |__$$ |$$ |  $$ |$$ |_____ $$ |  $$ |$$ |$$$/ $$ |$$ |_____ $$ |  $$ |
        $$    $$/ $$ |  $$ |$$       |$$ |  $$ |$$ | $/  $$ |$$       |$$ |  $$ |
        $$$$$$$/  $$/   $$/ $$$$$$$$/ $$/   $$/ $$/      $$/ $$$$$$$$/ $$/   $$/ 
        </>
        EOT;
        $this->output->writeln($header);
    }
}
