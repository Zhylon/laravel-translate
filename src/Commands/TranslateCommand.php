<?php

namespace Zhylon\LaravelTranslator\Commands;

use Illuminate\Console\Command;
use Zhylon\LaravelTranslator\Support\Facades\Translate;

class TranslateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:translate {target} {source?} {file?} {--file=} {--all} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate language files';

    private string $source;

    public function handle(): int
    {
        $this->source = $this->argument('source') ?? config('app.locale');
        $target = $this->argument('target');

        if (empty($this->source) || empty($target)) {
            $this->error('Source and target language must be provided.');

            return self::FAILURE;
        }

        if ($this->source == $target) {
            $this->error('Source and target language must be different.');

            return self::FAILURE;
        }

        $file = $this->argument('file') ?? $this->option('file');
        if (! $file && ! $this->option('all')) {
            $file = $this->source.'.json'; // default json file
        }

        // only json or php files allowed
        if ($file && ! in_array(pathinfo($file, PATHINFO_EXTENSION), ['json', 'php'])) {
            $this->error('Only json or php files are allowed.');

            return self::FAILURE;
        }

        if ($file) {
            return $this->doSingleFile($file, $target);
        }

        return $this->doAllFiles($target);
    }

    private function getContent(string $filename): array
    {
        if ('json' === pathinfo($filename, PATHINFO_EXTENSION)) {
            return json_decode(file_get_contents($filename), true);
        }

        return include $filename;
    }

    private function getFilePath(string $name, string $locale = ''): string
    {
        if (! empty($locale)) {
            $locale = $locale.'/';
        }
        $file = $locale.$name;

        return lang_path($file);
    }

    private function doSingleFile($filename, $target): int
    {
        if ($filename == $this->source.'.json') {
            if (! file_exists($filename = $this->getFilePath($filename))) {
                $this->error("File `$filename` does not exist.");

                return self::FAILURE;
            }
        } elseif (! file_exists($filename = $this->getFilePath($filename, $this->source))) {
            $this->error("File `$filename` does not exist.");

            return self::FAILURE;
        }

        $contents = $this->getContent($filename);
        $translated = $this->translateContent($contents, $this->source, $target);

        $this->storeNewContent($translated, $target, $filename);
        $this->successTable([[basename($filename)]]);
        $this->doStyleFix();

        return self::SUCCESS;
    }

    private function doAllFiles(string $target): int
    {
        $contents = collect($this->getFilesRecursive($this->getFilePath($this->source)))
            ->filter(fn ($file) => 'json' === pathinfo($file, PATHINFO_EXTENSION)
                || 'php' === pathinfo($file, PATHINFO_EXTENSION))
            ->map(fn ($file) => str_replace($this->getFilePath($this->source.'/'), '', $file))
            ->mapWithKeys(fn ($file) => [$file => $this->getContent($this->getFilePath($file, $this->source))])
            ->filter(fn ($content) => ! empty($content));

        $translatedItems = $this->translateContent($contents->toArray(), $this->source, $target);
        foreach ($translatedItems as $filename => $translated) {
            $this->storeNewContent($translated, $target, $this->getFilePath($filename, $this->source));
        }

        $this->successTable(
            collect($translatedItems)->keys()->map(fn ($file) => [$file])->toArray()
        );
        $this->doStyleFix();

        return self::SUCCESS;
    }

    private function getFilesRecursive(string $path): array
    {
        $files = [];
        foreach (glob($path.'/*') as $file) {
            if (is_dir($file)) {
                $files = array_merge($files, $this->getFilesRecursive($file));
            } else {
                $files[] = $file;
            }
        }

        return $files;
    }

    private function translateContent($contents, string $source, string $target): array
    {
        return Translate::transArray($contents, $source, $target);
    }

    private function getTranslationContent(array $content, string $file): string
    {
        if ('json' === pathinfo($file, PATHINFO_EXTENSION)) {
            return json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return var_export($content, true);
    }

    private function storeNewContent(array $translated, string $target, string $filename): void
    {
        $translateFile = str_replace($this->source, $target, $filename);
        $translatedContent = $this->getTranslationContent($translated, $translateFile);

        // make sure the directory exists, recursively
        if (! is_dir($dir = pathinfo($translateFile, PATHINFO_DIRNAME))) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($translateFile, "<?php\n\nreturn ".$translatedContent.";\n");
    }

    private function successTable(array $translated): void
    {
        $this->info('The following files have been translated successfully:');
        $this->table(['File'], $translated);
    }

    private function doStyleFix(): void
    {
        // recommend Laravel Pint
        $this->comment('We recommend to use Laravel Pint to fix your style issues after translation.');
        $this->comment('Use `composer require laravel/pint --dev` to install it.');
        $this->comment('Then run `./vendor/bin/pint lang` to fix your style issues.');
    }
}
