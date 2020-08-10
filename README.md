# oc-seeder-plugin

Laravel Seeder integration for October CMS.

This plugin integrates Laravel's Factory and Database Seeder features with October CMS.

## Defining factories

To define a new Factory for your plugin, create a `factories.php` in the plugin's root folder and define your factories [as you would in Laravel](https://laravel.com/docs/6.x/database-testing#writing-factories):

```php
<?php
// plugins/yourvendor/yourplugin/factories.php

/** @var $factory Illuminate\Database\Eloquent\Factory */
$factory->define(\YourVendor\YourPlugin\Models\YourModel::class, function (\OFFLINE\Seeder\Classes\Generator $faker) {
    return [
        'name' => $faker->name,
        'number' => $faker->numberBetween(0, 6),
    ];
});
```

## Defining seeders

Add a `registerSeeder` method to your `Plugin.php` in which you seed your plugin's models:

```php
public function registerSeeder()
{
    factory(\YourVendor\YourPlugin\Models\YourModel::class, 50)->create();
}
```

## Running seeders

Simply run `php artisan plugin:seed` to run the seeders of all plugins. 

Use the `--fresh` option to refresh all seeded plugins before popuplating them with new data. Be aware that this will rollback and reinstall a plugin completely, so any plugin data will be lost.

## Included factories

This plugin includes factories for the following models:

### `\System\Models\File::class`

`factory(\System\Models\File::class)->make()` returns a `File` model with a random image. You can use it in any seeder to attach a file to a created model:

```php
// Create a model
$myModel = factory(\YourVendor\YourPlugin\Models\YourModel::class)->create();

// Attach an image
$image = factory(\System\Models\File::class)->make();
$myModel->image()->save($image);
```

There are size states available: `tiny` returns a `90x90` image, `hd` returns a `1920x1080` image and `huge` returns a `6000x4000` image. 
Only one side of the image will match the given dimension (it is uncropped by default).


```php
$tiny = factory(\System\Models\File::class)->states('tiny')->create();
$hd = factory(\System\Models\File::class)->states('hd')->create();
$huge = factory(\System\Models\File::class)->states('huge')->create();
```

If you need something other than an image, you can use the `pdf`, `mp3` or `xlsx` states:

```php
$pdf = factory(\System\Models\File::class)->states('pdf')->create();
$mp3 = factory(\System\Models\File::class)->states('mp3')->create();
$xlsx = factory(\System\Models\File::class)->states('xlsx')->create();
```


### `\Backend\Models\User::class`

`factory(\Backend\Models\User::class)->make()` returns a Backend `User` model. You can use the `superuser`, `role:publisher` or `role:developer` states to generate a specific user type.

```php
// Build a simple backend user.
factory(\Backend\Models\User::class)->make();

// Build a superuser backend user.
factory(\Backend\Models\User::class)->states('superuser')->make();

// Build a backend user with the publisher role attached.
factory(\Backend\Models\User::class)->states('role:publisher')->make();
```

### `\RainLab\User\Models\User::class`

`factory(\RainLab\User\Models\User::class)->make()` returns a RainLab `User` model.


## Attribution

All images used in this plugin are provided by unsplash.com.


## Credits

This plugin is heavily inspired by [Inetis' oc-testing-plugin](https://github.com/inetis-ch/oc-testing-plugin).
