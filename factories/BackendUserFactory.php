<?php

namespace OFFLINE\Seeder\Factories;


use OFFLINE\Seeder\Classes\Factory;

class BackendUserFactory extends Factory
{
    public function definition()
    {
        $faker = fake();

        return [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'login' => $faker->userName,
            'email' => $faker->safeEmail,
            'password' => $password = $faker->password(8),
            'password_confirmation' => $password,
            'is_activated' => true,
            'activated_at' => now(),
            'role_id' => null,
            'is_superuser' => false,
        ];
    }

    public function superuser()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_superuser' => true,
            ];
        });
    }
}
