<?php

namespace OFFLINE\Seeder;

use Faker\Generator as FakerGenerator;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use OFFLINE\Seeder\Behaviors\HasSeederFactoryBehavior;
use OFFLINE\Seeder\Classes\FakerFactory;
use OFFLINE\Seeder\Classes\Generator;
use OFFLINE\Seeder\Classes\MultiFactory;
use OFFLINE\Seeder\Classes\OctoberCMSFakerProvider;
use OFFLINE\Seeder\Console\InitSeedCommand;
use OFFLINE\Seeder\Console\PluginSeedCommand;
use System\Classes\PluginBase;
use System\Models\File;


class Plugin extends PluginBase
{
    const FILE_TITLE = 'OFFLINE.Seeder File';

    const FILE_SIZES = ['tiny' => '90x90', 'default' => '1200x768', 'hd' => '1920x1080', 'huge' => '6000x4000'];
    const FILE_TYPES = ['mp3', 'pdf', 'xlsx'];

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
        $this->registerConsoleCommand('offline.seeder.init', InitSeedCommand::class);

        $fakerLocale = config()->get('app.faker_locale', 'en_US');
        $abstract = \Faker\Generator::class . ':' . $fakerLocale;

        // Bind our custom faker factory to the container.
        $this->app->singleton(
            $abstract,
            function () use ($fakerLocale) {
                return FakerFactory::create($fakerLocale);
            }
        );

        // Add model factories to external models.
        \System\Models\File::extend(function (\System\Models\File $file) {
            $file->extendClassWith(HasSeederFactoryBehavior::class);
        });

        \Backend\Models\User::extend(function (\Backend\Models\User $user) {
            $user->extendClassWith(HasSeederFactoryBehavior::class);
        });

        if (class_exists('\Tailor\Models\EntryRecord')) {
            \Tailor\Models\EntryRecord::extend(function (\Tailor\Models\EntryRecord $record) {
                $record->extendClassWith(HasSeederFactoryBehavior::class);
            });
        }

        if (class_exists(\RainLab\User\Models\User::class)) {
            \RainLab\User\Models\User::extend(function (\RainLab\User\Models\User $user) {
                $user->extendClassWith(HasSeederFactoryBehavior::class);
            });
        }
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'random_image' => function ($size = 'default') {
                    if (!array_key_exists($size, self::FILE_SIZES)) {
                        throw new \LogicException(
                            sprintf('Invalid file size "%s" passed to random_image helper, available are: %s', $size,
                                implode(', ', array_keys(self::FILE_SIZES)))
                        );
                    }

                    $size = self::FILE_SIZES[$size];

                    $file = File::where('title', self::FILE_TITLE . ' ' . $size)->inRandomOrder()->first();
                    if (!$file) {
                        throw new \LogicException('Run "php artisan seeder:init" before using the random_image helper.');
                    }

                    return $file;
                },
                'random_file' => function ($type = 'xlsx') {
                    if (!in_array($type, self::FILE_TYPES)) {
                        throw new \LogicException(
                            sprintf('Invalid file type "%s" passed to random_file helper, available are: %s', $type,
                                implode(', ', self::FILE_TYPES))
                        );
                    }
                    $file = File::where('title', self::FILE_TITLE . ' ' . $type)->inRandomOrder()->first();
                    if (!$file) {
                        throw new \LogicException('Run "php artisan seeder:init" before using the random_file helper.');
                    }

                    return $file;
                },
            ],
        ];
    }

}
