<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generate a migration inside a domain's Infrastructure/Database/Migrations folder.
 *
 * Usage:
 *   php artisan make:domain-migration Knowledge/create_tags_table
 *   php artisan make:domain-migration Task/add_priority_to_tasks_table --create=tasks
 *   php artisan make:domain-migration Knowledge/add_color_to_folders_table --table=folders
 */
final class MakeDomainMigration extends Command
{
    protected $signature = 'make:domain-migration
                            {path       : Domain/migration_name  e.g. Knowledge/create_tags_table}
                            {--create=  : Table name for a "create table" stub}
                            {--table=   : Table name for an "alter table" stub}';

    protected $description = 'Create a migration inside a domain\'s Infrastructure/Database/Migrations folder';

    public function handle(): int
    {
        [$domain, $name] = $this->parsePath($this->argument('path'));

        $timestamp = date('Y_m_d_His');
        $filename  = "{$timestamp}_{$name}.php";
        $className = Str::studly($name);

        $dir  = app_path("Domains/{$domain}/Infrastructure/Database/Migrations");
        $path = "{$dir}/{$filename}";

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if (File::exists($path)) {
            $this->warn("  Migration already exists: {$path}");
            return self::SUCCESS;
        }

        $stub = $this->buildStub($className);

        File::put($path, $stub);

        $this->newLine();
        $this->info("✓ Migration created: {$path}");
        $this->newLine();

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function parsePath(string $path): array
    {
        $parts = explode('/', $path);

        if (count($parts) !== 2) {
            $this->error('Path must be Domain/migration_name  e.g. Knowledge/create_tags_table');
            exit(1);
        }

        return [Str::studly($parts[0]), Str::snake($parts[1])];
    }

    private function buildStub(string $className): string
    {
        $create = $this->option('create');
        $table  = $this->option('table');

        if ($create) {
            return $this->createTableStub($className, $create);
        }

        if ($table) {
            return $this->alterTableStub($className, $table);
        }

        return $this->blankStub($className);
    }

    private function createTableStub(string $className, string $table): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table): void {
            \$table->id();
            // TODO: define columns
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    private function alterTableStub(string $className, string $table): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('{$table}', function (Blueprint \$table): void {
            // TODO: add / modify columns
        });
    }

    public function down(): void
    {
        Schema::table('{$table}', function (Blueprint \$table): void {
            // TODO: reverse changes
        });
    }
};
PHP;
    }

    private function blankStub(string $className): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TODO: implement
    }

    public function down(): void
    {
        // TODO: reverse
    }
};
PHP;
    }
}
