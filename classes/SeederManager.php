<?php

namespace OFFLINE\Seeder\Classes;

use Illuminate\Console\OutputStyle;
use October\Rain\Exception\ApplicationException;
use October\Rain\Support\Traits\Singleton;
use OFFLINE\Seeder\Models\Seed;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;
use Tailor\Classes\Blueprint;
use Tailor\Classes\BlueprintIndexer;
use Tailor\Models\EntryRecord;

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

        $hasError = false;

        $plugins->each(
            function ($plugin, $identifier) use ($updateManager, $fresh, &$hasError) {
                /** @var $plugin \System\Classes\PluginBase */
                if (method_exists($plugin, 'registerSeeder')) {
                    try {
                        $seeded = Seed::where(['seeder' => get_class($plugin)])->first();
                        if ($seeded && !$fresh) {
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
                    $this->write(sprintf('%-30s: No plugin seeders found', $identifier), true);
                }
            }
        );

        // Seed Tailor Blueprints.
        if (class_exists(\App\Provider::class)) {
            $this->write("\n\nSeeding Tailor data...\n", true);

            $blueprints = Blueprint::listInProject();
            if (count($filter) > 0) {
                $blueprints = $blueprints->filter(
                    function ($blueprint) use ($filter) {
                        return in_array(strtolower($blueprint->handle), $filter, true);
                    }
                );
            }

            $blueprints = $blueprints->keyBy('handle');

            if ($blueprints->count() > 0 && method_exists(\App\Provider::class, 'registerSeeder')) {
                \App\Provider::registerSeeder(function ($handle, $callback) use ($blueprints, $fresh, &$hasError) {
                    if (!$blueprints->has($handle)) {
                        return;
                    }

                    $seeded = Seed::where(['seeder' => $handle])->first();
                    if ($seeded && !$fresh) {
                        $this->write(sprintf('<info>%-30s: already seeded!</info>', $handle), true);

                        return;
                    }

                    if ($fresh) {
                        EntryRecord::inSection($handle)->get()->each->delete();
                    }

                    $factory = EntryRecord::tailorFactory($handle);

                    $extendWithBlueprint = function (EntryRecord $model) use ($handle) {

                        $blueprint = BlueprintIndexer::instance()->findSectionByHandle($handle);
                        if (!$blueprint) {
                            throw new ApplicationException("Section handle [{$handle}] not found");
                        }
                        $model->extendWithBlueprint($blueprint->uuid);

                        return $model;
                    };

                    $factory = $factory->afterMaking($extendWithBlueprint);
                    $factory = $factory->afterCreating($extendWithBlueprint);

                    $this->write(sprintf('<info>%-30s: -> seeding...</info>', $handle));
                    try {
                        $callback($factory);
                    } catch (\Throwable $e) {
                        logger()->error(sprintf('[Seeder for %s failed]: %s', $handle, $e));
                        $this->write('       <fg=white;bg=red>Failed!</>', true);
                        $this->write("\n<fg=white;bg=red>" . $e->getMessage() . "</>\n", true);
                        $this->write("<fg=blue>" . $e->getTraceAsString() . "</>\n", true);
                        $hasError = true;
                    }
                    $this->write('       <fg=black;bg=green>Done!</>', true);
                    Seed::create(['seeder' => $handle]);
                });
            } else {
                $this->write(sprintf('%-30s: No seeders found', ''), true);
            }
        }

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
