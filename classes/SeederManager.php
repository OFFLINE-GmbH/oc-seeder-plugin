<?php

namespace OFFLINE\Seeder\Classes;

use Illuminate\Console\OutputStyle;
use October\Rain\Support\Traits\Singleton;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;

class SeederManager
{
    use Singleton;

    protected $providers;

    protected $pluginManager;

    protected function init()
    {
        $this->pluginManager = PluginManager::instance();
    }

    public function seed(bool $fresh = false, OutputStyle $out = null)
    {
        if ($out) {
            $out->writeln("Seeding plugin data. This might take a while...\n");
        }
        $updateManager = UpdateManager::instance();
        collect(PluginManager::instance()->getPlugins())->each(
            function ($plugin, $identifier) use ($updateManager, $out, $fresh) {
                /** @var $plugin \System\Classes\PluginBase */
                if (method_exists($plugin, 'registerSeeder')) {
                    try {
                        if ($fresh) {
                            $updateManager->rollbackPlugin($identifier);
                            $updateManager->updatePlugin($identifier);
                        }
                        if ($out) {
                            $out->write(sprintf('<info>%-30s: -> seeding...</info>', $identifier));
                        }
                        $plugin->registerSeeder();
                        if ($out) {
                            $out->write('       <fg=black;bg=green>Done!</>', true);
                        }
                    } catch (\Throwable $e) {
                        logger()->error('OFFLINE.Seeder failed: ' . $e->getMessage(), [$e]);
                        if ($out) {
                            $out->write('       <fg=white;bg=red>Failed! (see log)</>', true);
                        }
                    }
                } elseif ($out) {
                    $out->writeln(sprintf('%-30s: No seeders found', $identifier));
                }
            }
        );
    }
}