<?php

namespace Zhylon\LaravelTranslator;

use Throwable;
use Illuminate\Support\Facades\Http;
use Zhylon\LaravelTranslator\Support\Exceptions\InvalidModelException;
use Zhylon\LaravelTranslator\Support\Exceptions\InvalidApiKeyException;
use Zhylon\LaravelTranslator\Support\Exceptions\InvalidApiCallException;

class TranslateManager
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function trans(array $texts, string $source = 'en', string $target = 'de'): array
    {
        return $this->makeApiCall($texts, $source, $target);
    }

    public function transArray(array $items, string $source = 'en', string $target = 'de'): array
    {
        return $this->makeApiCall($items, $source, $target, true);
    }

    private function makeApiCall(array $texts, string $source = 'en', string $target = 'de', bool $arrayType = false): array
    {
        $this->ensureApiKey();

        // https://translate.zhylon.net/docs
        $response = Http::withToken($this->config['key'])
            ->acceptJson()
            ->post('https://translate.zhylon.net/api/v1/'.$this->config['model'].'/translate', [
                'items'  => collect($texts)->take(2)->toArray(),
                'source' => $source,
                'target' => $target,
            ]);

        throw_if(
            401 === $response->status() || 403 === $response->status(),
            new InvalidApiKeyException('Invalid API key provided.')
        );

        throw_if(
            404 === $response->status(),
            new InvalidModelException('Invalid model or API endpoint provided.')
        );

        throw_unless(
            $response->successful(),
            new InvalidApiCallException('API call failed.')
        );

        return $response->json('data');
    }

    /**
     * @throws InvalidApiKeyException|Throwable
     */
    private function ensureApiKey(): void
    {
        throw_if(
            empty($this->config['key']),
            new InvalidApiKeyException('No API key provided.')
        );

        throw_if(
            empty($this->config['model']) || ! in_array($this->config['model'], ['deepl', 'zhylon']),
            new InvalidModelException('Invalid model provided.')
        );
    }
}
