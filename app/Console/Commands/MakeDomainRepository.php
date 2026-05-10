<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generate a Repository interface + Eloquent implementation for an existing model.
 *
 * Usage:
 *   php artisan make:domain-repository Knowledge/Folder
 */
final class MakeDomainRepository extends Command
{
    protected $signature = 'make:domain-repository
                            {path : Domain/ModelName  e.g. Knowledge/Folder}';

    protected $description = 'Scaffold a Repository interface + Eloquent implementation for a domain model';

    public function handle(): int
    {
        [$domain, $model] = $this->parsePath($this->argument('path'));

        $this->generateInterface($domain, $model);
        $this->generateImplementation($domain, $model);

        $this->newLine();
        $this->info("✓ Repository scaffolded for {$domain}/{$model}");
        $this->line("  Bind it in app/Domains/{$domain}/Providers/{$domain}ServiceProvider.php:");
        $this->line("    \$this->app->bind({$model}RepositoryInterface::class, Eloquent{$model}Repository::class);");
        $this->newLine();

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function parsePath(string $path): array
    {
        $parts = explode('/', $path);

        if (count($parts) !== 2) {
            $this->error('Path must be Domain/ModelName  e.g. Knowledge/Folder');
            exit(1);
        }

        return [Str::studly($parts[0]), Str::studly($parts[1])];
    }

    private function generateInterface(string $domain, string $model): void
    {
        $path = app_path("Domains/{$domain}/Domain/Contracts/{$model}RepositoryInterface.php");

        if (File::exists($path)) {
            $this->warn("  Interface already exists: {$path}");
            return;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Domains\\{$domain}\\Domain\\Contracts;

use App\\Domains\\{$domain}\\Models\\{$model};
use Illuminate\\Contracts\\Pagination\\LengthAwarePaginator;

interface {$model}RepositoryInterface
{
    public function findById(int \$id): ?{$model};

    public function paginateForUser(int \$userId, array \$filters = []): LengthAwarePaginator;

    public function create(array \$data): {$model};

    public function update({$model} \$model, array \$data): {$model};

    public function delete({$model} \$model): void;
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);
        $this->line("  <fg=green>CREATED</> {$path}");
    }

    private function generateImplementation(string $domain, string $model): void
    {
        $path = app_path("Domains/{$domain}/Infrastructure/Repositories/Eloquent{$model}Repository.php");

        if (File::exists($path)) {
            $this->warn("  Repository already exists: {$path}");
            return;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Domains\\{$domain}\\Infrastructure\\Repositories;

use App\\Domains\\{$domain}\\Domain\\Contracts\\{$model}RepositoryInterface;
use App\\Domains\\{$domain}\\Models\\{$model};
use Illuminate\\Contracts\\Pagination\\LengthAwarePaginator;

final class Eloquent{$model}Repository implements {$model}RepositoryInterface
{
    public function findById(int \$id): ?{$model}
    {
        return {$model}::find(\$id);
    }

    public function paginateForUser(int \$userId, array \$filters = []): LengthAwarePaginator
    {
        \$query = {$model}::query()
            ->forUser(\$userId)
            ->orderByDesc('created_at');

        // TODO: apply \$filters

        return \$query->paginate(20);
    }

    public function create(array \$data): {$model}
    {
        return {$model}::create(\$data);
    }

    public function update({$model} \$model, array \$data): {$model}
    {
        \$model->update(\$data);

        return \$model->fresh();
    }

    public function delete({$model} \$model): void
    {
        \$model->delete();
    }
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);
        $this->line("  <fg=green>CREATED</> {$path}");
    }
}
