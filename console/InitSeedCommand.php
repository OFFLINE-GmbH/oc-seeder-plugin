<?php

namespace OFFLINE\Seeder\Console;

use Illuminate\Console\Command;
use OFFLINE\Seeder\Models\SeederSettings;
use OFFLINE\Seeder\Plugin;
use Symfony\Component\Console\Input\InputOption;
use System\Models\File;

class InitSeedCommand extends Command
{
    protected $name = 'seeder:init';

    protected $description = 'Seeds random file attachments';

    public function handle()
    {
        $force = (bool)$this->option('force');
        if ((int)SeederSettings::get('seeded_at') && !$force) {
            if (!$this->confirm('Models are already seeded, do you want to proceed?')) {
                return;
            }
        }

        // Images
        $sizes = ['90x90', '1200x768', '1920x1080', '6000x4000'];
        foreach ($sizes as $size) {
            $folder = plugins_path('offline/seeder/assets/images/' . $size);
            $files = glob($folder . '/*.jpg');
            foreach ($files as $file) {
                $model = (new File)->fromFile($file);
                $model->title = Plugin::FILE_TITLE . ' ' . $size;
                $model->save();
            }
        }

        // File types
        $folder = plugins_path('offline/seeder/assets/files/');
        $files = glob($folder . '/*');
        foreach ($files as $file) {
            $ext = pathinfo($file)['extension'];
            $model = (new File)->fromFile($file);
            $model->title = Plugin::FILE_TITLE . ' ' . $ext;
            $model->save();
        }

        SeederSettings::set('seeded_at', time());
    }

    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Seed even if it was seeded before', null],
        ];
    }
}
