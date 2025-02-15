<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallNewsModules extends Command
{
    protected $signature = 'aristechnews:install:modules {--modules=all : Liste des modules à installer (séparés par une virgule, ou "all" pour tout installer) --stack=blade : Stack à utiliser (blade ou react)}';
    protected $description = 'Installe les modules du package NewsManager';

    protected $modules = [
        'news' => [
            'controllers' => ['NewsController.php'],
            'models' => ['News.php'],
            'routes' => 'news.php',
            'migrations' => ['0002_02_02_00001_create_news_table.php']
        ],
        'media' => [
            'controllers' => ['MediaController.php'],
            'models' => ['Media.php'],
            'routes' => 'media.php',
            'migrations' => ['0002_02_02_00003_create_medias_table.php']
        ],
        'documents' => [
            'controllers' => ['DocumentController.php'],
            'models' => ['Document.php'],
            'routes' => 'documents.php',
            'migrations' => ['0002_02_02_00002_create_reports_table.php']
        ]
    ];

    public function handle(): void
    {
        $stack = $this->option('stack');
        $modules = $this->option('modules');

        $this->info("Installation des modules pour la stack {$stack}...");

        $modulesToInstall = [];

        if ($modules === 'all') {
            $modulesToInstall = array_keys($this->modules);
        } else {
            $modulesToInstall = explode(',', $modules);
        }

        foreach ($modulesToInstall as $moduleName) {
            if (!isset($this->modules[$moduleName])) {
                $this->error("Le module {$moduleName} n'existe pas.");
                continue;
            }

            $moduleComponents = $this->modules[$moduleName];
            if ($this->confirm("Voulez-vous installer le module {$moduleName} ?")) {
                $this->installModule($moduleName, $moduleComponents, $stack);
            }
        }

        // Mise à jour du fichier web.php
        $this->updateWebRoutes($modulesToInstall, $stack);
    }

    /**
     * Met à jour le fichier web.php avec les routes des modules
     */
    protected function updateWebRoutes(array $installedModules, string $stack): void
    {
        $this->info("Mise à jour des routes dans web.php...");
        
        $webPhpPath = base_path('routes/web.php');
        
        // Créer une sauvegarde du fichier web.php original
        if (File::exists($webPhpPath)) {
            File::copy($webPhpPath, $webPhpPath . '.backup');
        }

        // Copier le template web.php de base selon la stack
        $templatePath = __DIR__ . "/../../../routes/{$stack}/all/web.php";
        if (!File::exists($templatePath)) {
            $this->error("Le template de routes pour la stack {$stack} n'existe pas.");
            return;
        }

        // Copier le template web.php
        File::copy($templatePath, $webPhpPath);

        // Pour chaque module, copier ses routes dans le dossier approprié
        foreach ($installedModules as $module) {
            if (!isset($this->modules[$module])) {
                continue;
            }

            $moduleRouteFile = __DIR__ . "/../../../routes/{$stack}/{$module}/{$this->modules[$module]['routes']}";
            
            if (File::exists($moduleRouteFile)) {
                // Créer le répertoire des routes du module si nécessaire
                $moduleRoutesDir = base_path("routes/{$module}");
                if (!File::isDirectory($moduleRoutesDir)) {
                    File::makeDirectory($moduleRoutesDir, 0755, true);
                }

                // Copier le fichier de routes du module
                $destinationPath = "{$moduleRoutesDir}/{$this->modules[$module]['routes']}";
                if (File::copy($moduleRouteFile, $destinationPath)) {
                    $this->info("Routes du module {$module} copiées avec succès.");
                } else {
                    $this->error("Erreur lors de la copie des routes du module {$module}");
                }
            } else {
                $this->warn("Fichier de routes introuvable pour le module {$module}");
            }
        }

        $this->info('Le fichier web.php a été mis à jour avec succès.');
    }

    protected function installModule(string $moduleName, array $components, string $stack): void
    {
        $this->info("Installation du module {$moduleName}...");

        // Copier les models (commun à toutes les stacks)
        if (isset($components['models'])) {
            foreach ($components['models'] as $model) {
                $source = __DIR__ . "/../../../src/Models/{$moduleName}/{$model}";
                $destination = app_path("Models/{$moduleName}");
                $this->copyFileIfExists($source, $destination, "Model {$model}");
            }
        }

        // Copier les routes
        if (isset($components['routes'])) {
            $source = __DIR__ . "/../../../routes/{$stack}/{$moduleName}/{$components['routes']}";
            $destination = base_path("routes/{$moduleName}");
            $this->copyFileIfExists($source, $destination, "Routes du module {$moduleName}");
        }

        // Copier les migrations
        if (isset($components['migrations'])) {
            foreach ($components['migrations'] as $migration) {
                $source = __DIR__ . "/../../../database/migrations/{$migration}";
                $destination = database_path("migrations/{$migration}");
                $this->copyFileIfExists($source, $destination, "Migration {$migration}");
            }
        }

        $this->info("Module {$moduleName} installé avec succès.");
    }

    // Méthodes utilitaires copyFileIfExists et copyDirectoryIfExists comme avant...
}