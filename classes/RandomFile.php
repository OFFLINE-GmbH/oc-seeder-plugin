<?php

namespace OFFLINE\Seeder\Classes;


use System\Models\File;

class RandomFile
{
    public static function image(string $size = 'default')
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

        return (new File)->fromFile(array_random($files));
    }

    public static function file(string $type = 'xlsx')
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

        return (new File)->fromFile(plugins_path('offline/seeder/assets/files/' . $file));
    }
}