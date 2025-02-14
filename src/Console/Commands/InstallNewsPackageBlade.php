<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;

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
        $this->info('Installation du package NewsManager pour la stack Blade.');

        // Vérifier et installer Laravel Breeze s'il n'est pas présent
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

        // Copier l'ensemble des vues se trouvant dans le dossier resources/Blade/views du package
        $sourceViews      = __DIR__ . '/../../resources/Blade';
        $destinationViews = resource_path('views/vendor/newsmanager/Blade');

        if (!File::isDirectory($destinationViews)) {
            File::makeDirectory($destinationViews, 0755, true);
        }

        if (File::copyDirectory($sourceViews, $destinationViews)) {
            $this->info('Les vues Blade ont été copiées dans ' . $destinationViews);
        } else {
            $this->error('La copie des vues Blade a échoué.');
        }

        // Appeler la commande Breeze pour installer le stack Blade
        $this->call('breeze:install', ['stack' => 'blade']);

        $this->info('Installation du package NewsManager pour Blade terminée.');
    }
}