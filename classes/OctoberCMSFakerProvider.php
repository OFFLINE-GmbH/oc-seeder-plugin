<?php

namespace OFFLINE\Seeder\Classes;


use Faker\Provider\Base;
use System\Models\File;

class OctoberCMSFakerProvider extends Base
{
    public function ocImage(string $size = 'default')
    {
        switch ($size) {
            case 'tiny':
                $folder = '90x90';
                break;
            case 'hd':
                $folder = '1920x1080';
                break;
            case 'huge':
                $folder = '6000x4000';
                break;
            default:
                $folder = '1200x768';
        }

        $folder = plugins_path('offline/seeder/assets/images/' . $folder);
        $files = glob($folder . '/*.jpg');

        $file = (new File)->fromFile(array_random($files));

        return $file->getLocalPath();
    }

    public function ocFile(string $type = 'xlsx')
    {
        switch ($type) {
            case 'mp3':
                $file = 'example.mp3';
                break;
            case 'pdf':
                $file = 'example.pdf';
                break;
            case 'xlsx':
                $file = 'example.xlsx';
                break;
            default:
                $file = array_random(['example.mp3', 'example.pdf', 'example.xlsx']);
        }


        $file = (new File)->fromFile(plugins_path('offline/seeder/assets/files/' . $file));

        return $file->getLocalPath();
    }
}