<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generate a new HMAC API key for OpenAPI documentation access.
 *
 * Usage:
 *   php artisan api:generate-key open_api
 *   php artisan api:generate-key open_api --inactive
 */
final class GenerateApiKey extends Command
{
    protected $signature = 'api:generate-key
                            {name : A label to identify this key (e.g. open_api, postman)}
                            {--inactive : Create the key in inactive state}';

    protected $description = 'Generate a new API key for OpenAPI documentation access';

    public function handle(): int
    {
        $name   = $this->argument('name');
        $key    = 'ak_' . Str::random(32);
        $secret = Str::random(64);

        DB::table('api_keys')->insert([
            'name'       => $name,
            'key'        => $key,
            'secret'     => $secret,
            'is_active'  => !$this->option('inactive'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->newLine();
        $this->info('✓ API Key generated successfully!');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Name',   $name],
                ['Key',    $key],
                ['Secret', $secret],
                ['Active', $this->option('inactive') ? 'No' : 'Yes'],
            ]
        );

        $this->newLine();
        $this->warn('⚠  Save the Secret now — it will NOT be shown again.');
        $this->newLine();

        return self::SUCCESS;
    }
}
