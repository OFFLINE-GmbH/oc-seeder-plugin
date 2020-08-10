<?php

namespace OFFLINE\Seeder;

use Faker\Generator as FakerGenerator;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use OFFLINE\Seeder\Classes\FakerFactory;
use OFFLINE\Seeder\Classes\Generator;
use OFFLINE\Seeder\Classes\MultiFactory;
use OFFLINE\Seeder\Classes\OctoberCMSFakerProvider;
use OFFLINE\Seeder\Console\PluginSeedCommand;
use System\Classes\PluginBase;


class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Seeder',
            'description' => 'Seed plugin data from the command line',
            'author' => 'OFFLINE',
            'icon' => 'icon-rocket',
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('offline.seeder.seed', PluginSeedCommand::class);

        // Bind our custom faker factory to the container.
        $this->app->singleton(
            Generator::class,
            function ($app) {
                return FakerFactory::create($app['config']->get('app.faker_locale', 'en_US'));
            }
        );
        // Build the Eloquent factory with our custom generator.
        $this->app->singleton(
            EloquentFactory::class,
            function ($app) {
                /** @var FakerGenerator $faker */
                $faker = $app->make(Generator::class);
                $faker->addProvider(new OctoberCMSFakerProvider($faker));

                return MultiFactory::constructMany($faker, 'factory.php');
            }
        );
    }
}
