<?php

namespace OFFLINE\Seeder\Factories;

use OFFLINE\Seeder\Classes\Factory;
use OFFLINE\Seeder\Classes\OctoberCMSFakerProvider;

class FileFactory extends Factory
{
    protected function fake() {
        /** @var OctoberCMSFakerProvider $faker */
        $faker = fake();

        return $faker;
    }
    public function definition()
    {
        return [
            'data' => $this->fake()->ocImage(),
        ];
    }

    public function tiny()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => $this->fake()->ocImage('tiny'),
            ];
        });
    }

    public function hd()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => $this->fake()->ocImage('hd'),
            ];
        });
    }

    public function huge()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => $this->fake()->ocImage('huge'),
            ];
        });
    }

    public function pdf()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => $this->fake()->ocFile('pdf'),
            ];
        });
    }

    public function xlsx()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => $this->fake()->ocFile('xlsx'),
            ];
        });
    }

    public function mp3()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => $this->fake()->ocFile('mp3'),
            ];
        });
    }

    public function file()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => $this->fake()->ocFile('file'),
            ];
        });
    }
}
