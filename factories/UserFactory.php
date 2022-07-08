<?php

namespace OFFLINE\Seeder\Factories;


use OFFLINE\Seeder\Classes\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        $faker = fake();

        return [
            'name' => $faker->name,
            'surname' => $faker->lastName,
            'email' => $email = $faker->safeEmail,
            'password' => $password = $faker->password(8),
            'password_confirmation' => $password,
            'is_activated' => true,
            'activated_at' => now(),
            'username' => $email,
            'is_guest' => false,
            'is_superuser' => false,
        ];
    }
}
