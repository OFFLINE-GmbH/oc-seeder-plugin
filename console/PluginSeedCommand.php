<?php

namespace OFFLINE\Seeder\Console;

use Illuminate\Console\Command;
use OFFLINE\Seeder\Classes\SeederManager;
use Symfony\Component\Console\Input\InputOption;

class PluginSeedCommand extends Command
{
    protected $name = 'offline:seeder';

    protected $description = 'Runs all plugin seeders';

    public function handle()
    {
        $fresh = (bool)$this->option('fresh');
        $filter = collect(explode(',', $this->option('plugins')))->map(
            function ($identifier) {
                return strtolower(trim($identifier));
            }
        )->filter()->toArray();

        $manager = SeederManager::instance();
        $manager->setOutput($this->output);
        $manager->seed($fresh, $filter);
    }

    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['fresh', null, InputOption::VALUE_NONE, 'Remove any existing plugin data before running a seeder', null],
            [
                'plugins',
                null,
                InputOption::VALUE_OPTIONAL,
                'Run a specific seeder. You can separate multiple plugins by a comma',
                null,
            ],
        ];
    }
}
