<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generate a domain Data (DTO) class.
 *
 * Usage:
 *   php artisan make:domain-data Knowledge/FolderData
 *   php artisan make:domain-data Auth/LoginResult
 */
final class MakeDomainData extends Command
{
    protected $signature = 'make:domain-data
                            {path : Domain/ClassName  e.g. Knowledge/FolderData}';

    protected $description = 'Scaffold a domain Data (DTO) class — immutable readonly value object';

    public function handle(): int
    {
        $parts = array_map(fn ($p) => Str::studly($p), explode('/', $this->argument('path')));

        if (count($parts) !== 2) {
            $this->error('Path must be Domain/ClassName  e.g. Knowledge/FolderData');
            return self::FAILURE;
        }

        [$domain, $name] = $parts;

        $path = app_path("Domains/{$domain}/Data/{$name}.php");

        if (File::exists($path)) {
            $this->warn("  Data class already exists: {$path}");
            return self::SUCCESS;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Domains\\{$domain}\\Data;

/**
 * {$name}
 *
 * Immutable Data Transfer Object.
 * Carries validated input between layers — no business logic here.
 */
final readonly class {$name}
{
    public function __construct(
        // TODO: define properties
        // public int \$userId,
        // public string \$title,
    ) {}

    /**
     * Construct from a validated array (e.g. \$request->validated()).
     */
    public static function fromArray(array \$data): self
    {
        return new self(
            // TODO: map array keys to constructor args
            // userId: (int) \$data['user_id'],
            // title: (string) \$data['title'],
        );
    }

    /**
     * Serialize back to a plain array (e.g. for Eloquent::create / update).
     */
    public function toArray(): array
    {
        return [
            // TODO: map properties to array keys
            // 'user_id' => \$this->userId,
            // 'title'   => \$this->title,
        ];
    }
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        $this->newLine();
        $this->info("✓ Data class created: {$path}");
        $this->newLine();

        return self::SUCCESS;
    }
}
