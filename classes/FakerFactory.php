<?php

namespace OFFLINE\Seeder\Classes;


use Faker\Factory;

class FakerFactory extends Factory
{
    /**
     * Create a new (custom) generator
     *
     * @return Generator
     */
    public static function create($locale = self::DEFAULT_LOCALE)
    {
        $generator = new Generator();

        foreach (static::$defaultProviders as $provider) {
            $providerClassName = self::getProviderClassname($provider, $locale);
            $generator->addProvider(new $providerClassName($generator));
        }

        $generator->addProvider(new OctoberCMSFakerProvider($generator));

        return $generator;
    }
}
