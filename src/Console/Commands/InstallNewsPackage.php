<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;

class InstallNewsPackage extends Command
{
    /**
     * La signature de la commande.
     *
     * Vous pouvez passer en option le stack et les modules, sinon la commande demande interactivement.
     *
     * @var string
     */
    protected $signature = 'breeze:news {--stack= : La stack frontale à utiliser pour Breeze} {--modules= : Modules à installer (séparés par une virgule, ou "all" pour tout installer)}';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Commande artisan interactive pour installer le package NewsManager, configurer Laravel Breeze et choisir les modules à installer';

    /**
     * Exécute la commande.
     */
    public function handle()
    {
        // Vérifier si Laravel Breeze est installé
        if (!class_exists(\Laravel\Breeze\BreezeServiceProvider::class)) {
            $this->error('Laravel Breeze n\'est pas installé. Veuillez l\'installer via Composer :');
            $this->line('    composer require laravel/breeze');
            return;
        }
        
        $this->info('Début de l\'installation du package NewsManager.');

        // 1. Choix de la stack pour Breeze
        $stack = $this->option('stack');
        if (!$stack) {
            $stack = $this->choice(
                'Quelle stack frontale souhaitez-vous installer pour Laravel Breeze ?',
                ['blade', 'react', 'vue'],
                0
            );
        }
        $this->info("Stack sélectionnée : " . $stack);

        // Appel de la commande de Breeze pour installer le stack sélectionné
        // (la commande breeze:install doit être bien configurée dans votre application)
        $this->call('breeze:install', ['stack' => $stack]);

        // 2. Choix des modules à installer
        $availableModules = ['news', 'media', 'documents'];
        $modulesInput = $this->option('modules');

        if ($modulesInput) {
            // Si l'utilisateur passe une option, on gère le "all" ou une liste séparée par des virgules
            if (strtolower($modulesInput) === 'all') {
                $modules = $availableModules;
            } else {
                $inputModules = array_map('trim', explode(',', $modulesInput));
                $modules = array_intersect($inputModules, $availableModules);
            }
        } else {
            // Sinon, proposer un choix interactif
            if ($this->confirm('Voulez-vous installer tous les modules (actualités, médias, documents) ?', true)) {
                $modules = $availableModules;
            } else {
                $modules = [];
                if ($this->confirm('Installer le module Actualités ?', false)) {
                    $modules[] = 'news';
                }
                if ($this->confirm('Installer le module Médias ?', false)) {
                    $modules[] = 'media';
                }
                if ($this->confirm('Installer le module Documents ?', false)) {
                    $modules[] = 'documents';
                }
            }
        }

        if (empty($modules)) {
            $this->warn('Aucun module sélectionné. Le package sera installé sans modules additionnels.');
        } else {
            $this->info('Modules sélectionnés : ' . implode(', ', $modules));
            // Publication de la configuration et des migrations spécifiques.
            // Vous pouvez définir des tags spécifiques pour chaque module dans votre Service Provider
            foreach ($modules as $module) {
                switch ($module) {
                    case 'news':
                        $this->call('vendor:publish', [
                            '--tag' => 'newsmanager-config',
                        ]);
                        $this->call('vendor:publish', [
                            '--tag' => 'newsmanager-migrations',
                        ]);
                        break;
                    case 'media':
                        // Si vous avez des assets ou configurations spécifiques pour les médias,
                        // vous pouvez définir un tag "newsmanager-media-config"
                        $this->call('vendor:publish', [
                            '--tag' => 'newsmanager-media-config',
                        ]);
                        break;
                    case 'documents':
                        // Pareil pour le module documents
                        $this->call('vendor:publish', [
                            '--tag' => 'newsmanager-documents-config',
                        ]);
                        break;
                }
            }
        }

        $this->info('Installation du package NewsManager terminée.');
        $this->info('Pensez à lancer "php artisan migrate" pour exécuter les migrations.');
    }
} 