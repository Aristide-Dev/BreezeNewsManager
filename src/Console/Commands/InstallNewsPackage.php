<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InstallNewsPackage extends Command
{
    /**
     * La signature de la commande.
     *
     * Vous pouvez passer en option le stack et les modules,
     * sinon la commande demande interactivement.
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
    public function handle(): void
    {
        // Vérifier si Laravel Breeze est installé
        if (!class_exists(\Laravel\Breeze\BreezeServiceProvider::class)) {
            $this->error("Laravel Breeze n'est pas installé. Installation automatique en cours...");

            // utilisation de Symfony Process pour exécuter "composer require laravel/breeze"
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

        $this->info("Début de l'installation du package NewsManager.");

        // 1. Détermination de la stack à utiliser
        $stack = $this->option('stack');
        if (!$stack) {
            $stack = $this->autoDetectStack();
            if ($stack) {
                $this->info("Stack auto-détectée : $stack");
            } else {
                $stack = $this->choice(
                    'Quelle stack frontale souhaitez-vous installer pour Laravel Breeze ?',
                    ['blade', 'react', 'vue'],
                    0
                );
            }
        } else {
            $this->info("Stack spécifiée par option : $stack");
        }

        // Appel de la commande Breeze pour installer le stack sélectionné
        $this->call('breeze:install', ['stack' => $stack]);

        // 2. Choix des modules à installer (après l'installation du stack)
        $availableModules = ['news', 'media', 'documents'];
        $modulesOption  = $this->option('modules');

        if ($modulesOption) {
            $modules = (strtolower($modulesOption) === 'all')
                ? $availableModules
                : array_intersect(
                    array_map('trim', explode(',', $modulesOption)),
                    $availableModules
                );
        } else {
            if ($this->confirm('Voulez-vous installer tous les modules (actualités, médias, documents) ?', true)) {
                $modules = $availableModules;
            } else {
                $modules = [];
                foreach ($availableModules as $module) {
                    if ($this->confirm('Installer le module ' . ucfirst($module) . ' ?', false)) {
                        $modules[] = $module;
                    }
                }
            }
        }

        if (empty($modules)) {
            $this->warn('Aucun module sélectionné. Le package sera installé sans modules additionnels.');
        } else {
            $this->info('Modules sélectionnés : ' . implode(', ', $modules));
            // Association de chaque module aux tags de publication correspondants
            $moduleTags = [
                'news'      => ['newsmanager-config', 'newsmanager-migrations'],
                'media'     => ['newsmanager-media-config'],
                'documents' => ['newsmanager-documents-config'],
            ];

            foreach ($modules as $module) {
                if (isset($moduleTags[$module])) {
                    foreach ($moduleTags[$module] as $tag) {
                        $this->call('vendor:publish', ['--tag' => $tag]);
                    }
                }
            }
        }

        $this->info('Installation du package NewsManager terminée.');
        $this->info('Pensez à lancer "php artisan migrate" pour exécuter les migrations.');
    }

    /**
     * Tente d'auto-détecter la stack à utiliser.
     *
     * @return string|null
     */
    private function autoDetectStack(): ?string
    {
        // Heuristique simple : détecte Blade, Vue ou React en fonction de l'existence de certains fichiers.
        if (file_exists(resource_path('views/auth/login.blade.php'))) {
            return 'blade';
        } elseif (file_exists(resource_path('js/Pages/Login.vue'))) {
            return 'vue';
        } elseif (file_exists(resource_path('js/Components/Login.jsx'))) {
            return 'react';
        }
        return null;
    }
}
