<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generate a domain FormRequest.
 *
 * Usage:
 *   php artisan make:domain-request Knowledge/CreateFolderRequest
 */
final class MakeDomainRequest extends Command
{
    protected $signature = 'make:domain-request
                            {path : Domain/RequestName  e.g. Knowledge/CreateFolderRequest}';

    protected $description = 'Scaffold a domain FormRequest';

    public function handle(): int
    {
        $parts = array_map(fn ($p) => Str::studly($p), explode('/', $this->argument('path')));

        if (count($parts) !== 2) {
            $this->error('Path must be Domain/RequestName  e.g. Knowledge/CreateFolderRequest');
            return self::FAILURE;
        }

        [$domain, $name] = $parts;

        $path = app_path("Domains/{$domain}/Requests/{$name}.php");

        if (File::exists($path)) {
            $this->warn("  Request already exists: {$path}");
            return self::SUCCESS;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Domains\\{$domain}\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

final class {$name} extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // TODO: define validation rules
        ];
    }
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        $this->newLine();
        $this->info("✓ Request created: {$path}");
        $this->newLine();

        return self::SUCCESS;
    }
}
