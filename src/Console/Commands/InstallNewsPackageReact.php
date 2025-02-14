<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InstallNewsPackageReact extends Command
{
    /**
     * La signature de la commande.
     *
     * @var string
     */
    protected $signature = 'news:install:react';

    /**
     * La description de la commande.
     *
     * @var string
     */
    protected $description = 'Commande d\'installation du package NewsManager pour la stack React';

    /**
     * Exécute la commande.
     */
    public function handle(): void
    {
        $this->info('Installation du package NewsManager pour la stack React.');

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

        // Copier le répertoire de vues pour React
        $sourceViews      = __DIR__ . '/../../resources/React';
        $destinationViews = resource_path('views/vendor/newsmanager/React');

        if (!File::isDirectory($destinationViews)) {
            File::makeDirectory($destinationViews, 0755, true);
        }

        if (File::copyDirectory($sourceViews, $destinationViews)) {
            $this->info('Les vues React ont été copiées dans ' . $destinationViews);
        } else {
            $this->error('La copie des vues React a échoué.');
        }

        // Appeler la commande Breeze pour installer la stack React
        $this->call('breeze:install', ['stack' => 'react']);

        $this->info('Installation du package NewsManager pour React terminée.');
    }
} 