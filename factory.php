<?php

/** @var $factory Illuminate\Database\Eloquent\Factory */

$factory->define(
    \System\Models\File::class,
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocImage(),
        ];
    }
);

$factory->state(
    \System\Models\File::class,
    'tiny',
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocImage('tiny'),
        ];
    }
);
$factory->state(
    \System\Models\File::class,
    'hd',
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocImage('hd'),
        ];
    }
);
$factory->state(
    \System\Models\File::class,
    'huge',
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocImage('huge'),
        ];
    }
);
$factory->state(
    \System\Models\File::class,
    'pdf',
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocFile('pdf'),
        ];
    }
);
$factory->state(
    \System\Models\File::class,
    'xlsx',
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocFile('xlsx'),
        ];
    }
);
$factory->state(
    \System\Models\File::class,
    'mp3',
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocFile('mp3'),
        ];
    }
);
$factory->state(
    \System\Models\File::class,
    'file',
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
        return [
            'data' => $faker->ocFile('any'),
        ];
    }
);

$factory->define(
    \RainLab\User\Models\User::class,
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
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
);

$factory->define(
    \Backend\Models\User::class,
    function (\OFFLINE\Seeder\Classes\Generator $faker) {
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
);

$factory->state(
    \Backend\Models\User::class,
    'superuser',
    [
        'is_superuser' => true,
    ]
);

$factory->state(
    \Backend\Models\User::class,
    'role:publisher',
    function () {
        $role = \Backend\Models\UserRole::where('code', 'publisher')->first();

        return ['role_id' => $role->id ?? null];
    }
);

$factory->state(
    \Backend\Models\User::class,
    'role:developer',
    function () {
        $role = \Backend\Models\UserRole::where('code', 'developer')->first();

        return ['role_id' => $role->id ?? null];
    }
);