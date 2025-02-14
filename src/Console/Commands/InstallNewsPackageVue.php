<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InstallNewsPackageVue extends Command
{
    protected $signature = 'aristechnews:install:vue';
    protected $description = 'Commande d\'installation du package NewsManager pour la stack Vue';

    public function handle(): void
    {
        $this->info('Installation du package NewsManager pour la stack Vue.');

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

        // Copier le répertoire de vues pour VueJS
        $sourceViews = __DIR__ . '/../../resources/VueJs';
        $destinationViews = resource_path('views/vendor/newsmanager/VueJs');
        if (!File::isDirectory($destinationViews)) {
            File::makeDirectory($destinationViews, 0755, true);
        }
        if (File::copyDirectory($sourceViews, $destinationViews)) {
            $this->info('Les vues Vue ont été copiées dans ' . $destinationViews);
        } else {
            $this->error('La copie des vues Vue a échoué.');
        }

        // Copier les routes pour VueJS
        $sourceRoutes = __DIR__ . '/../../routes/VueJS';
        $destinationRoutes = base_path('routes/newsmanager/VueJS');
        if (!File::isDirectory($destinationRoutes)) {
            File::makeDirectory($destinationRoutes, 0755, true);
        }
        if (File::copyDirectory($sourceRoutes, $destinationRoutes)) {
            $this->info('Les routes Vue ont été copiées dans ' . $destinationRoutes);
        } else {
            $this->error('La copie des routes Vue a échoué.');
        }

        // Appeler la commande Breeze pour installer la stack Vue
        $this->call('breeze:install', ['stack' => 'vue', '--no-interaction' => true]);

        $this->info('Installation du package NewsManager pour Vue terminée.');
    }
}
