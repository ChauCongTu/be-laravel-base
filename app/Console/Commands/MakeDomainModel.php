<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generate a DDD domain model with its Eloquent repository.
 *
 * Usage:
 *   php artisan make:domain-model Knowledge/Folder
 *   php artisan make:domain-model Task/Task --soft-deletes
 */
final class MakeDomainModel extends Command
{
    protected $signature = 'make:domain-model
                            {path : Domain/ModelName  e.g. Knowledge/Folder}
                            {--soft-deletes : Add SoftDeletes trait to the model}';

    protected $description = 'Scaffold a domain Model + Repository interface + Eloquent implementation';

    public function handle(): int
    {
        [$domain, $model] = $this->parsePath($this->argument('path'));

        $this->generateModel($domain, $model);
        $this->generateRepositoryInterface($domain, $model);
        $this->generateEloquentRepository($domain, $model);

        $this->newLine();
        $this->info("✓ Domain model scaffolded for {$domain}/{$model}");
        $this->line("  Remember to bind the interface in app/Domains/{$domain}/Providers/{$domain}ServiceProvider.php");
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

    private function generateModel(string $domain, string $model): void
    {
        $path = app_path("Domains/{$domain}/Models/{$model}.php");

        if (File::exists($path)) {
            $this->warn("  Model already exists: {$path}");
            return;
        }

        $softDeletes = $this->option('soft-deletes')
            ? "use SoftDeletes;\n\n    "
            : '';

        $softDeletesImport = $this->option('soft-deletes')
            ? "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n"
            : '';

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\\Domains\\{$domain}\\Models;

use App\\Models\\User;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
{$softDeletesImport}
final class {$model} extends Model
{
    {$softDeletes}use HasFactory;

    protected \$fillable = [
        'user_id',
        // TODO: add fillable fields
    ];

    protected \$casts = [
        'user_id'    => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return \$this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForUser(\$query, int \$userId)
    {
        return \$query->where('user_id', \$userId);
    }

    // -------------------------------------------------------------------------
    // Business rules
    // -------------------------------------------------------------------------

    public function canBeEditedBy(?int \$userId): bool
    {
        return \$this->user_id === \$userId;
    }

    public function canBeViewedBy(?int \$userId): bool
    {
        return \$this->user_id === \$userId;
    }
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);
        $this->line("  <fg=green>CREATED</> {$path}");
    }

    private function generateRepositoryInterface(string $domain, string $model): void
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

    private function generateEloquentRepository(string $domain, string $model): void
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
