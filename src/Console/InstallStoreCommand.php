<?php

namespace Netto\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallStoreCommand extends Command
{
    protected $signature = 'cms:install-store';
    protected $description = 'Install Nettoweb CMS online store';

    /**
     * @return void
     */
    public function handle(): void
    {
        $fileSystem = new Filesystem();

        $fileSystem->copyDirectory(__DIR__.'/../../stub/app', app_path());
        $fileSystem->copy(__DIR__.'/../../stub/config/cms-store.php', config_path('cms-store.php'));
        $fileSystem->copyDirectory(__DIR__.'/../../stub/resources/views/admin', resource_path('views/admin'));

        $this->components->info('Nettoweb CMS store was successfully installed');
    }
}
