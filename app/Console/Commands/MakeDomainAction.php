<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generate a domain Action class.
 *
 * Usage:
 *   php artisan make:domain-action Knowledge/Folder/CreateFolderAction
 *   php artisan make:domain-action Task/SendTaskReminderAction
 */
final class MakeDomainAction extends Command
{
    protected $signature = 'make:domain-action
                            {path : Domain[/SubDir]/ActionName  e.g. Knowledge/Folder/CreateFolderAction}';

    protected $description = 'Scaffold a domain Action class';

    public function handle(): int
    {
        $parts = array_map(fn ($p) => Str::studly($p), explode('/', $this->argument('path')));

        if (count($parts) < 2) {
            $this->error('Path must be at least Domain/ActionName');
            return self::FAILURE;
        }

        $domain     = array_shift($parts);
        $actionName = array_pop($parts);
        $subDir     = implode('/', $parts); // may be empty

        $namespaceSuffix = $subDir
            ? str_replace('/', '\\', $subDir)
            : '';

        $namespace = $namespaceSuffix
            ? "App\\Domains\\{$domain}\\Domain\\{$namespaceSuffix}"
            : "App\\Domains\\{$domain}\\Domain";

        $relPath = $subDir
            ? "Domains/{$domain}/Domain/{$subDir}/{$actionName}.php"
            : "Domains/{$domain}/Domain/{$actionName}.php";

        $path = app_path($relPath);

        if (File::exists($path)) {
            $this->warn("  Action already exists: {$path}");
            return self::SUCCESS;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

final readonly class {$actionName}
{
    public function __construct(
        // TODO: inject repository or other dependencies
    ) {}

    public function execute(): void
    {
        // TODO: implement
    }
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        $this->newLine();
        $this->info("✓ Action created: {$path}");
        $this->newLine();

        return self::SUCCESS;
    }
}
