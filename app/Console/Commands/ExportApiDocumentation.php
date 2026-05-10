<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Dedoc\Scramble\Generator;
use Dedoc\Scramble\Scramble;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Exports the OpenAPI JSON spec to storage/api-docs/openapi.json.
 *
 * Usage:
 *   php artisan api:export-docs
 *   php artisan api:export-docs --pretty   (formatted JSON)
 */
final class ExportApiDocumentation extends Command
{
    protected $signature = 'api:export
                            {--pretty : Pretty-print the JSON output}';

    protected $description = 'Export OpenAPI documentation to storage/api-docs/openapi.json';

    public function handle(Generator $generator): int
    {
        $config = Scramble::getGeneratorConfig(Scramble::DEFAULT_API);

        $spec = $generator($config);

        $json = $this->option('pretty')
            ? json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : json_encode($spec, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $dir  = storage_path('api-docs');
        $path = $dir . '/openapi.json';

        File::ensureDirectoryExists($dir);
        File::put($path, $json);

        $this->info("OpenAPI spec exported → {$path}");
        $this->line('  Size: ' . number_format(strlen($json)) . ' bytes');

        return self::SUCCESS;
    }
}
