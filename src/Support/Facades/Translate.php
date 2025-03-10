<?php

namespace Zhylon\LaravelTranslator\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array transArray(array $items, string $source = 'en', string $target = 'de')
 * @method static array trans(array $texts, string $source = 'en', string $target = 'de')
 */
class Translate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'translate';
    }
}
