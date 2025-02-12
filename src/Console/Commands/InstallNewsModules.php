<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;

class InstallNewsModules extends Command
{
    /**
     * La signature de la commande.
     *
     * @var string
     */
    protected $signature = 'news:modules';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Commande artisan pour choisir et installer les modules du package NewsManager';

    /**
     * Exécute la commande.
     */
    public function handle(): void
    {
        $this->info('Installation des modules du package NewsManager.');

        // Liste des modules disponibles et les tags à publier pour chacun
        $availableModules = [
            'news'      => ['newsmanager-config', 'newsmanager-migrations'],
            'media'     => ['newsmanager-media-config'],
            'documents' => ['newsmanager-documents-config'],
        ];

        // Demande à l'utilisateur s'il veut installer tous les modules
        if ($this->confirm('Voulez-vous installer tous les modules (actualités, médias, documents) ?', true)) {
            $modulesToInstall = array_keys($availableModules);
        } else {
            $modulesToInstall = [];
            foreach ($availableModules as $module => $tags) {
                if ($this->confirm('Installer le module ' . ucfirst($module) . ' ?', false)) {
                    $modulesToInstall[] = $module;
                }
            }
        }

        if (empty($modulesToInstall)) {
            $this->warn('Aucun module sélectionné. Aucun module ne sera installé.');
            return;
        }

        $this->info('Modules sélectionnés : ' . implode(', ', $modulesToInstall));

        // Publication des ressources pour chaque module sélectionné
        foreach ($modulesToInstall as $module) {
            foreach ($availableModules[$module] as $tag) {
                $this->call('vendor:publish', ['--tag' => $tag]);
            }
        }

        $this->info('Installation des modules terminée.');
    }
} 