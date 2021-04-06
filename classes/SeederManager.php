<?php

namespace OFFLINE\Seeder\Classes;

use Illuminate\Console\OutputStyle;
use October\Rain\Support\Traits\Singleton;
use OFFLINE\Seeder\Models\Seed;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;

class SeederManager
{
    use Singleton;

    protected $providers;
    protected $pluginManager;
    protected $output;

    protected function init()
    {
        $this->pluginManager = PluginManager::instance();
    }

    public function setOutput(OutputStyle $out = null)
    {
        $this->output = $out;
    }

    public function seed(bool $fresh = false, array $filter = [])
    {
        $this->write("Seeding plugin data. This might take a while...\n", true);

        $updateManager = UpdateManager::instance();
        $plugins = collect(PluginManager::instance()->getPlugins());

        if (count($filter) > 0) {
            $plugins = $plugins->filter(
                function ($_, $identifier) use ($filter) {
                    return in_array(strtolower($identifier), $filter, true);
                }
            );
        }

        if ($plugins->count() < 1) {
            if ($this->output) {
                $this->output->note('Nothing to seed!');
            }

            return;
        }

        $hasError = false;

        $plugins->each(
            function ($plugin, $identifier) use ($updateManager, $fresh, &$hasError) {
                /** @var $plugin \System\Classes\PluginBase */
                if (method_exists($plugin, 'registerSeeder')) {
                    try {
                        $seeded = Seed::where(['seeder' => get_class($plugin)])->first();
                        if ($seeded && ! $fresh) {
                            $this->write(sprintf('<info>%-30s: already seeded!</info>', $identifier), true);

                            return;
                        }
                        if ($fresh) {
                            $updateManager->rollbackPlugin($identifier);
                            $updateManager->updatePlugin($identifier);
                        }
                        $this->write(sprintf('<info>%-30s: -> seeding...</info>', $identifier));
                        $plugin->registerSeeder();
                        $this->write('       <fg=black;bg=green>Done!</>', true);
                    } catch (\Throwable $e) {
                        logger()->error(sprintf('[Seeder for %s failed]: %s', $identifier, $e));
                        $this->write('       <fg=white;bg=red>Failed!</>', true);
                        $this->write("\n<fg=white;bg=red>" . $e->getMessage() . "</>\n", true);
                        $this->write("<fg=blue>" . $e->getTraceAsString() . "</>\n", true);
                        $hasError = true;
                    }
                    Seed::create(['seeder' => get_class($plugin)]);
                } else {
                    $this->write(sprintf('%-30s: No seeders found', $identifier), true);
                }
            }
        );

        if ($hasError) {
            $this->write("\n<fg=red>There was an error with at least one seeder.</>", true);
            $this->write("<fg=red>Check the console output or your system.log file for more details.</>\n", true);
        }
    }

    protected function write(string $data, bool $newLine = false)
    {
        if ($this->output) {
            $this->output->write($data, $newLine);
        }
    }
}
