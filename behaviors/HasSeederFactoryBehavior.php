<?php

namespace OFFLINE\Seeder\Behaviors;


use OFFLINE\Seeder\Classes\Factory;

class HasSeederFactoryBehavior extends \October\Rain\Extension\ExtensionBase
{
    public $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function factory($count = null, $state = [])
    {
        $factory = Factory::factoryForModel($this->parent::class);

        return self::buildFactory($factory, $count, $state);
    }

    public function tailorFactory(string $handle, $count = null, $state = [])
    {
        $factory = Factory::factoryForModel($this->parent::class, $handle);

        return self::buildFactory($factory, $count, $state);
    }

    private static function buildFactory($factory, $count, $state)
    {
        return $factory
            ->count(is_numeric($count) ? $count : null)
            ->state(is_callable($count) || is_array($count) ? $count : $state);
    }
}
