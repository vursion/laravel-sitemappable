# Laravel Sitemappable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vursion/laravel-sitemappable.svg?style=flat-square)](https://packagist.org/packages/vursion/laravel-sitemappable)
![Tests](https://github.com/vursion/laravel-sitemappable/workflows/tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/vursion/laravel-sitemappable.svg?style=flat-square)](https://packagist.org/packages/vursion/laravel-sitemappable)

## Installation

You can install the package via composer:

```bash
composer require vursion/laravel-sitemappable
```

***No need to register the service provider if you're using Laravel >= 5.5.
The package will automatically register itself.***
Once the package is installed, you can register the service provider in `config/app.php` in the providers array:
```php
'providers' => [
    ...
    Vursion\LaravelSitemappable\SitemappableServiceProvider::class
],
```

You need to publish the migration with:
```bash
php artisan vendor:publish --provider="Vursion\LaravelSitemappable\SitemappableServiceProvider" --tag=migrations
```

You should publish the `config/sitemappable.php` config file with:
```bash
php artisan vendor:publish --provider="Vursion\LaravelSitemappable\SitemappableServiceProvider" --tag=config
```

This is the content of the published config file:

```php
return [

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Sitemappable model shipped with this package.
     */
    'db_table_name' => 'sitemap',

    /*
     * The generated XML sitemap is cached to speed up performance.
     */
    'cache' => '60 minutes',

    /*
     * The batch import will loop through this directory and search for models
     * that use the IsSitemappable trait.
     */
    'model_directory' => 'app/Models',

    /*
     * If you're extending the controller, you'll need to specify the new location here.
     */
    'controller' => Vursion\LaravelSitemappable\Http\Controllers\SitemappableController::class,

];
```

## Making a model sitemappable

The required steps to make a model sitemappable are:
- Add the `Vursion\LaravelSitemappable\IsSitemappable` trait.
- Define a public method `toSitemappableArray` that returns an array with the (localized) URL(s).
- Optionally define the conditions when a model should be sitemappable in a public method `shouldBeSitemappable`.

Here's an example of a model:

```php
use Illuminate\Database\Eloquent\Model;
use Vursion\LaravelSitemappable\IsSitemappable;

class YourModel extends Model
{
    use IsSitemappable;

    public function toSitemappableArray()
    {
        return [];
    }

    public function shouldBeSitemappable()
    {
        return true;
    }
}
```

### toSitemappableArray

You need to return an array with (localized) URL(s) of your model.

```php
public function toSitemappableArray()
{
    return [
        'nl' => 'https://www.vursion.io/nl/testen/test-slug-in-het-nederlands',
        'en' => 'https://www.vursion.io/en/tests/test-slug-in-english',
    ];
}
```

This is an example of a model that uses [ARCANDEDEV\Localization](https://github.com/ARCANEDEV/Localization)
for localized routes in combination with [spatie\laravel-translatable](https://github.com/spatie/laravel-translatable)
for making Eloquent models translatable.

```php
public function toSitemappableArray()
{
    return collect(localization()->getSupportedLocalesKeys())->mapWithKeys(function ($key) {
        return [$key => localization()->getUrlFromRouteName($key, 'routes.your-route-name', ['slug' => $this->getTranslationWithoutFallback('slug', $key)])];
    });
}
```
### shouldBeSitemappable (conditionally sitemappable model instances)

Sometimes you may need to only make a model sitemappable under certain conditions.
For example, imagine you have a `App\Models\Posts\Post` model.
You may only want to allow "non-draft" and "published" posts to be sitemappable.
To accomplish this, you may define a `shouldBeSitemappable` method on your model:

```php
public function shouldBeSitemappable()
{
    return (! $this->draft && $this->published);
}
```

## Rebuild the sitemap from scratch

If you are installing Laravel Sitemappable into an existing project, you may already have database records you need to import into your sitemap.
Laravel Sitemappable provides a `sitemappable:import` Artisan command that you may use to import all of your existing records into your sitemap:

```bash
php artisan sitemappable:import
```

## Adding non-model associated routes

It's very likely your project will have routes that are not associated with a model.
You can add these URLs by extending the controller and returning them via the `otherRoutes` method.

To publish the controller to `app/Http/Controllers/SitemappableController.php` run:

```bash
php artisan vendor:publish --provider="Vursion\LaravelSitemappable\SitemappableServiceProvider" --tag=controllers
```

Don't forget to change the location of the controller in the `config/sitemappable.php` config file:

```php
return [

    ...

    /*
     * If you're extending the controller, you'll need to specify the new location here.
     */
    'controller' => App\Http\Controllers\SitemappableController::class,

    ...

];
```

Just make sure you return an array of arrays with key/value pairs like the example below:

```php
public function otherRoutes()
{
    return [
        [
            'nl' => 'https://www.vursion.io/nl/contacteer-ons',
            'en' => 'https://www.vursion.io/en/contact-us',
        ],
        ...
    ];
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email jochen@celcius.be instead of using the issue tracker.

## Credits

- [Jochen Sengier](https://github.com/celcius-jochen)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
