<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InstallNewsPackageBlade extends Command
{
    /**
     * La signature de la commande.
     *
     * @var string
     */
    protected $signature = 'news:install:blade';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Commande d\'installation du package NewsManager pour la stack Blade';

    /**
     * Exécute la commande.
     */
    public function handle(): void
    {
        $this->info('=== Installation du package NewsManager pour la stack Blade ===');

        // 1. Vérifier et installer Laravel Breeze si nécessaire
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

        // 2. Copier les Controllers spécifiques Blade
        $sourceControllers = __DIR__ . '/../../src/Controllers/Blade';
        $destinationControllers = app_path('Http/Controllers');
        $this->copyDirectoryIfExists($sourceControllers, $destinationControllers, 'Controllers Blade');

        // 3. Copier les Routes spécifiques Blade
        $sourceRoutes = __DIR__ . '/../../routes/Blade';
        $destinationRoutes = base_path('routes');
        $this->copyDirectoryIfExists($sourceRoutes, $destinationRoutes, 'Routes Blade');

        // 4. Copier les Vues spécifiques Blade
        $sourceViews = __DIR__ . '/../../resources/Blade';
        $destinationViews = resource_path('views/vendor');
        $this->copyDirectoryIfExists($sourceViews, $destinationViews, 'Vues Blade');

        // 5. Appeler la commande Breeze pour installer la stack Blade
        $this->info("Installation de Laravel Breeze pour la stack Blade...");
        $this->call('breeze:install', [
            'stack' => 'blade',
            '--no-interaction' => true,
        ]);

        $this->info("Installation du package NewsManager pour Blade terminée.");
        $this->info("N'oubliez pas d'exécuter 'php artisan migrate' et 'npm install && npm run dev' pour finaliser l'installation.");
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
