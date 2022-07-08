<?php

namespace OFFLINE\Seeder\Factories;

use OFFLINE\Seeder\Classes\Factory;

class FileFactory extends Factory
{
    public function definition()
    {
        return [
            'data' => fake()->ocImage(),
        ];
    }

    public function tiny()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => fake()->ocImage('tiny'),
            ];
        });
    }

    public function hd()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => fake()->ocImage('hd'),
            ];
        });
    }

    public function huge()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => fake()->ocImage('huge'),
            ];
        });
    }

    public function pdf()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => fake()->ocFile('pdf'),
            ];
        });
    }

    public function xlsx()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => fake()->ocFile('xlsx'),
            ];
        });
    }

    public function mp3()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => fake()->ocFile('mp3'),
            ];
        });
    }

    public function file()
    {
        return $this->state(function (array $attributes) {
            return [
                'data' => fake()->ocFile('file'),
            ];
        });
    }
}
