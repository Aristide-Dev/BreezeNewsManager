<?php

namespace AristechDev\NewsManager\Console\Commands;

use Illuminate\Console\Command;

class InstallNewsPackage extends Command
{
    /**
     * La signature de la commande.
     *
     * @var string
     */
    protected $signature = 'news-manager:install';

    /**
     * Description de la commande.
     *
     * @var string
     */
    protected $description = 'Commande artisan interactive pour installer le package NewsManager';

    /**
     * Exécute la commande.
     */
    public function handle()
    {
        $this->info('Début de l\'installation du package NewsManager.');

        if ($this->confirm('Voulez-vous publier les fichiers de configuration ?')) {
            $this->call('vendor:publish', [
                '--tag' => 'newsmanager-config'
            ]);
        }

        if ($this->confirm('Voulez-vous publier les migrations ?')) {
            $this->call('vendor:publish', [
                '--tag' => 'newsmanager-migrations'
            ]);
        }

        $this->info('Installation terminée.');
    }
} 