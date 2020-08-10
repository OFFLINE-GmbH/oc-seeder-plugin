<?php

namespace OFFLINE\Seeder\Console;

use Illuminate\Console\Command;
use OFFLINE\Seeder\Classes\SeederManager;
use Symfony\Component\Console\Input\InputOption;

class PluginSeedCommand extends Command
{
    protected $name = 'plugin:seed';

    protected $description = 'Runs all plugin seeders';

    public function handle()
    {
        $fresh = (bool)$this->option('fresh');

        $manager = SeederManager::instance();
        $manager->seed($fresh, $this->output);
    }

    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['fresh', null, InputOption::VALUE_NONE, 'Remove any existing plugin data before running a seeder', null],
        ];
    }
}
