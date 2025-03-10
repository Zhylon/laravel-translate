# Zhylon Translatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zhylon/laravel-translate.svg?style=flat-square)](https://packagist.org/packages/zhylon/laravel-translate)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/zhylon/laravel-translate.svg?style=flat-square)](https://packagist.org/packages/zhylon/laravel-translate)
[![Support me on Patreon](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Fshieldsio-patreon.vercel.app%2Fapi%3Fusername%3DTobymaxham%26type%3Dpatrons&style=flat)](https://patreon.com/Tobymaxham)

This package is build on top of [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable).
We strongly recommend you to check the original package before using this one.

The key difference is that this package uses [Zhylon Translation](https://translate.zhylon.net/docs) to translate the content.
So you don't need to provide the translations, the package will do it for you.


## Installation

You can install the package via composer:

```bash
composer require zhylon/laravel-translate
```

If you only want to translate your language files, you can use the `--dev` flag to install the package as a development dependency:

```bash
composer require --dev zhylon/laravel-translate
```


## Configuration

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Zhylon\LaravelTranslator\LaravelTranslatorServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    'zhylon_translate' => [
        'key'   => env('ZHYLON_TRANSLATE_KEY'), // Your Zhylon Translate API key
        'model' => env('ZHYLON_TRANSLATE_MODEL', 'zhylon'), // The name of the model to translate
    ],
];
 ```

## Zhylon Translate API Key

You need an active account on [Zhylon Translate](https://translate.zhylon.net) to get the API key.
You can get the API key from the [API Key](https://translate.zhylon.net/user/api-tokens) page.

Please note, that this is not a free service, you need to have a subscription to use the service.
More information about the pricing can be found in the [Docs](https://translate.zhylon.net/docs).


## Usage

You can translate your language files using the following command:

```bash
# Translate single file
php artisan lang:translate en --file=billing.php

# Using path
php artisan lang:translate en --file=Module/billing.php

# Translate all files
php artisan lang:translate en --all
```


Also, all your translation files can be translated using **Zhylon Translation**.


```php
use Illuminate\Database\Eloquent\Model;
use Zhylon\LaravelTranslator\Support\Traits\HasAutoTranslations;

class NewsItem extends Model
{
    use HasAutoTranslations;
    
    public $translatable = ['name']; // translatable attributes

    // ...
}
```

### WARNING !

Currently, the package only supports english and german languages.
This is because also the Zhylon Translation API only supports these languages.
We are working on adding more languages to the API, so please check the [Zhylon Translation](https://translate.zhylon.net/docs) page for updates.


### Original Package

You can still use all the features of the original package, but you don't need to provide the translations.
For more information, please check the original package documentation: [spatie/laravel-translatable](https://spatie.be/docs/laravel-translatable)


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security Vulnerabilities

If you've found a bug regarding security please mail [security@zhylon.net](mailto:security@zhylon.net) instead of using the issue tracker.


## Support me

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/Z8Z4NZKU)<br>
[![Support me on Patreon](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Fshieldsio-patreon.vercel.app%2Fapi%3Fusername%3DTobymaxham%26type%3Dpatrons&style=flat)](https://patreon.com/Tobymaxham)


## Credits

- [TobyMaxham](https://github.com/TobyMaxham)
- [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
