<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generate a domain JsonResource.
 *
 * Usage:
 *   php artisan make:domain-resource Knowledge/FolderResource
 */
final class MakeDomainResource extends Command
{
    protected $signature = 'make:domain-resource
                            {path : Domain/ResourceName  e.g. Knowledge/FolderResource}';

    protected $description = 'Scaffold a domain JsonResource';

    public function handle(): int
    {
        $parts = array_map(fn ($p) => Str::studly($p), explode('/', $this->argument('path')));

        if (count($parts) !== 2) {
            $this->error('Path must be Domain/ResourceName  e.g. Knowledge/FolderResource');
            return self::FAILURE;
        }

        [$domain, $name] = $parts;

        $path = app_path("Domains/{$domain}/Resources/{$name}.php");

        if (File::exists($path)) {
            $this->warn("  Resource already exists: {$path}");
            return self::SUCCESS;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Domains\\{$domain}\\Resources;

use Illuminate\\Http\\Request;
use Illuminate\\Http\\Resources\\Json\\JsonResource;

final class {$name} extends JsonResource
{
    public function toArray(Request \$request): array
    {
        return [
            'id'         => \$this->id,
            // TODO: add fields
            'created_at' => \$this->created_at?->toIso8601String(),
            'updated_at' => \$this->updated_at?->toIso8601String(),
        ];
    }
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        $this->newLine();
        $this->info("✓ Resource created: {$path}");
        $this->newLine();

        return self::SUCCESS;
    }
}
