# oc-seeder-plugin

Laravel Seeder integration for October CMS.

This plugin integrates Laravel's Factory and Database Seeder features with October CMS.

## Installation

1. Install the plugin using Composer.

```bash
composer require offline/oc-seeder-plugin
```

2. Setup the random file helpers.

```bash
php artisan seeder:init
```

## Defining factories

To define a new Factory for your plugin, create a `YourModelFactory.php` in the plugin's `factories` folder and define
your factories [as you would in Laravel](https://laravel.com/docs/9.x/database-testing#defining-model-factories):

```php
<?php
// plugins/yourvendor/yourplugin/factories/YourModelFactory.php
namespace YourVendor\YourPlugin\Factories;

class YourModelFactory extends \OFFLINE\Seeder\Classes\Factory
{
    /**
     * Default model attributes.
     */
    public function definition()
    {
        return [
            'name' => fake()->name,
            'number' => fake()->numberBetween(0, 6),
        ];
    }

    /**
     * Define states: Override attributes that differ from the default definition.
     * Use it like this: YourModel::factory()->withHigherNumber()->make();
     */
    public function withHigherNumber()
    {
        return $this->states(function(array $attributes) {
            return [
                'number' => fake()->numberBetween(100, 900);
            ];
        });
    }
}

```


Next, add the `OFFLINE\Seeder\Traits\HasSeederFactory` trait to your model:

```php
<?php
// plugins/yourvendor/yourplugin/models/YourModel.php
namespace YourVendor\YourPlugin\Models;

class YourModel extends Model
{
    use \OFFLINE\Seeder\Traits\HasSeederFactory; // add this
}
```

## Defining seeders

Add a `registerSeeder` method to your `Plugin.php` in which you seed your plugin's models:

```php
public function registerSeeder()
{
    \YourVendor\YourPlugin\Models\YourModel::factory()->count(50)->create();
}
```

## Tailor integration

Starting from version 2.1, this plugin can be used to seed Tailor data.

### Requirements

To seed Tailor data using this plugin, it is required that all Tailor blueprint handles are in the `SomeSection\SomeEntity` format, like `Blog\Post`.

### Defining factories

For a Tailor entity like `Blog\Post`, create the file `app/factories/blog/PostFactory.php` and define your factory in it.

```php
<?php

namespace App\Factories\Blog;

class PostFactory extends \OFFLINE\Seeder\Classes\Factory
{
    public function definition()
    {
        return [
            'title' => fake()->sentence,
            // ...
            'is_enabled' => true,
        ];
    }
}
```

### Registering seeders

In the `app/Provider.php`, add a static `registerSeeder` method and define your seeders like so:

```php
<?php

namespace App;

use OFFLINE\Seeder\Classes\Factory;

class Provider extends \System\Classes\AppBase
{
    // ...

    /**
     * @param $seed \Closure(string $handle, \Closure(Factory $factory) $callback): void
     */
    public static function registerSeeder(\Closure $seed)
    {
        // Blog\Post = blueprint handle
        $seed('Blog\Post', function(Factory $factory) {
            $factory->count(10)->create();
        });

        $seed('Blog\Category', function(Factory $factory) {
            // ...
        });

        $seed('Blog\Author', function(Factory $factory) {
            // ...
        });
    }
```

### Seeding specific blueprints

The `--plugins` flag can be used to seed specific entities:

```bash
php artisan offline:seeder --plugins=Blog\\Post
```

## Migrate from 2.x

October 3.3 introduced its own `plugin:seed` Artisan command. To 
resolve this conflict, the Artisan command of this plugin was 
renamed to `offline:seeder`.

No special migration work is required, you can just use the new 
Artisan command.

## Migrate from 1.x

To migrate old seeders from Version 1.0 of this plugin, make the following changes:

1. Move all factories from the `factories.php` to their own `Factory` classes in the `factories` directory.
2. Add the `OFFLINE\Seeder\Traits\HasSeederFactory` trait to all models
3. Change your `registerSeeder` method:

```php
// Old
factory(YourModel::class)->make();
factory(YourModel::class)->states('special')->make();
// New
YourModel::factory()->make();
YourModel::factory()->special()->make();
```

## Running seeders

Simply run `php artisan offline:seeder` to run the seeders of all plugins. The seeder of each plugin will be only run once.

To run a seeder for a already seeded plugin, use the `--fresh` option. Be aware that this will rollback and reinstall
all plugins with a registered seeder completely, so any plugin data will be lost.

You can use the `--plugins` option to run only specified seeders. Simply provide a comma-separated list of plugin names.

```
php artisan offline:seeder --plugins=Vendor.PluginA,Vendor.PluginB --fresh
```

## Included factories

This plugin includes factories for the following models:

### `\System\Models\File::class`

`\System\Models\File::factory()->make()` returns a `File` model with a random image. You can use it in any seeder
to attach a file to a created model:

```php
// Create a model
$myModel = \YourVendor\YourPlugin\Models\YourModel::factory()->create();

// Attach an image
$image = \System\Models\File::factory()->make();
$myModel->image()->save($image);
```

There are size states available: `tiny` returns a `90x90` image, `hd` returns a `1920x1080` image and `huge` returns
a `6000x4000` image.
Only one side of the image will match the given dimension (it is uncropped by default).

```php
$tiny = \System\Models\File::factory()->tiny()->make();
$hd = \System\Models\File::factory()->hd()->make();
$huge = \System\Models\File::factory()->huge()->make();
```

If you need something other than an image, you can use the `file`, `pdf`, `mp3` or `xlsx` states:

```php
$randomType = \System\Models\File::factory()->file()->make();
$pdf = \System\Models\File::factory()->pdf()->make();
$mp3 = \System\Models\File::factory()->mp3()->make();
$xlsx = \System\Models\File::factory()->xlsx()->make();
```

### `\Backend\Models\User::class`

`\Backend\Models\User::factory()->make()` returns a Backend `User` model. You can use the `superuser`
 state to generate a superuser.

```php
// Build a simple backend user.
\Backend\Models\User::factory()->make();

// Build a superuser backend user.
\Backend\Models\User::factory()->superuser()->make();
```

### `\RainLab\User\Models\User::class`

`\RainLab\User\Models\User::factory()->make()` returns a RainLab `User` model.

## Twig helper functions

You can use the `random_image()` helper function to get a random image directly in your markup.

```twig
<img src="{{ random_image().thumb(400, 800) }}" alt=""/>
<img src="{{ random_image('tiny').thumb(400, 800) }}" alt=""/>
<img src="{{ random_image('hd').thumb(400, 800) }}" alt=""/>
<img src="{{ random_image('huge').thumb(400, 800) }}" alt=""/>
```

If you need a valid file download, you can use the `random_file()` function:

```twig
<a href="{{ random_file('xlsx').path }}" download>Download the spreadsheet!</a>
<a href="{{ random_file('pdf').path }}" download>Download the PDF!</a>

{# or make some noise #}
<audio controls src="{{ random_file('mp3').path }}"></audio>
```

## Attribution

All images used in this plugin are provided by [unsplash.com](https://unsplash.com).

## Credits

This plugin is heavily inspired by [Inetis' oc-testing-plugin](https://github.com/inetis-ch/oc-testing-plugin).
