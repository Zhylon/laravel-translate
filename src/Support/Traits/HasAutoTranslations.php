<?php

namespace Zhylon\LaravelTranslator\Support\Traits;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Zhylon\LaravelTranslator\Support\Facades\Translate;

/**
 * @mixin Eloquent
 */
trait HasAutoTranslations
{
    use HasTranslations;

    protected static function bootHasAutoTranslations()
    {
        static::saving(function ($model) {
            /** @var self $model */
            $model->checkTranslations($model->getTranslatableAttributes());
        });
    }

    protected function checkTranslations(array $attributes = []): void
    {
        if (empty($attributes)) {
            return;
        }

        $source = config('app.locale');
        $checkTrans = [];
        foreach ($attributes as $attribute) {
            if ($this->isDirty($attribute) && is_string($this->{$attribute})) {
                $checkTrans[$attribute] = $this->{$attribute};
            }
        }

        if (empty($checkTrans)) {
            return;
        }

        $target = 'de' == $source ? 'en' : 'de';
        $results = Translate::trans($checkTrans, $source, $target);
        if (count($results) > 0) {
            foreach ($results as $key => $value) {
                $this->{$key} = [
                    $source => $this->{$key},
                    $target => $value,
                ];
            }
        }
    }

    protected function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }
}
