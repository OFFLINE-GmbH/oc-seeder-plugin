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
    public static function constructMany(Faker $faker, string $pattern)
    {
        return (new static($faker))->loadMany($pattern);
    }

    /**
     * Load factories from multiple paths.
     *
     * @return $this
     */
    public function loadMany(string $pattern)
    {
        $factory = $this;

        foreach (Finder::create()->files()->name($pattern)->in(plugins_path()) as $file) {
            require $file->getRealPath();
        }

        return $factory;
    }
}