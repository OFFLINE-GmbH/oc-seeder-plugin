<?php

namespace OFFLINE\Seeder\Classes;


use Faker\Provider\Base;

class OctoberCMSFakerProvider extends Base
{
    public function ocImage(string $size = 'default')
    {
        $file = RandomFile::image($size);

        return $file->getLocalPath();
    }

    public function ocFile(string $type = 'xlsx')
    {
        $file = RandomFile::file($type);

        return $file->getLocalPath();
    }
}
