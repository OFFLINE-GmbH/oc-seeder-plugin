<?php

namespace OFFLINE\Seeder\Behaviors;


use OFFLINE\Seeder\Classes\Factory;
use OFFLINE\Seeder\Traits\HasSeederFactory;

class HasSeederFactoryBehavior extends \October\Rain\Extension\ExtensionBase
{
    protected $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get a new factory instance for the model.
     *
     * @param callable|array|int|null $count
     * @param callable|array          $state
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    public function factory($count = null, $state = [])
    {
        $factory = Factory::factoryForModel($this->parent::class);

        return $factory
            ->count(is_numeric($count) ? $count : null)
            ->state(is_callable($count) || is_array($count) ? $count : $state);
    }
}
