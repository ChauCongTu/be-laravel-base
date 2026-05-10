<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Auth\Providers\AuthServiceProvider;
use App\Domains\Knowledge\Providers\KnowledgeServiceProvider;
use App\Domains\Task\Providers\TaskServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(KnowledgeServiceProvider::class);
        $this->app->register(TaskServiceProvider::class);
    }

    public function boot(): void
    {
        // Disable Scramble's auto-registered /docs/api and /docs/api.json routes.
        Scramble::ignoreDefaultRoutes();

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }
}
