<?php

namespace OFFLINE\Seeder\Classes;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Symfony\Component\Finder\Finder;

class MultiFactory extends Factory
{
    /**
     * Create a new factory container with multiple sources.
     *
     * @return static
     */
    public static function constructMany(Faker $faker)
    {
        return (new static($faker))->loadMany();
    }

    /**
     * Load factories from multiple paths.
     *
     * @return $this
     */
    public function loadMany()
    {
        $factory = $this;

        $files = Finder::create()->files()->name('factory.php')->name('factories.php')->in(plugins_path());

        foreach ($files as $file) {
            require $file->getRealPath();
        }

        return $factory;
    }
}
