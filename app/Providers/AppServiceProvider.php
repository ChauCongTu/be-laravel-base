<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Auth\Providers\AuthServiceProvider;
use App\Domains\Knowledge\Providers\KnowledgeServiceProvider;
use App\Domains\Task\Providers\TaskServiceProvider;
use Carbon\Carbon;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
        // ---------------------------------------------------------------
        // Domain migrations
        // ---------------------------------------------------------------
        $this->loadMigrationsFrom([
            app_path('Domains/Auth/Infrastructure/Database/Migrations'),
            app_path('Domains/Knowledge/Infrastructure/Database/Migrations'),
            app_path('Domains/Task/Infrastructure/Database/Migrations'),
        ]);

        // ---------------------------------------------------------------
        // Passport token expiry
        // ---------------------------------------------------------------
        Passport::tokensExpireIn(
            Carbon::now()->addDays(config('passport.token_expire_days', 1))
        );

        Passport::refreshTokensExpireIn(
            Carbon::now()->addDays(config('passport.refresh_token_expire_days', 30))
        );

        Passport::personalAccessTokensExpireIn(
            Carbon::now()->addMonths(6)
        );

        Passport::enablePasswordGrant();

        // ---------------------------------------------------------------
        // Scramble — OpenAPI docs
        // ---------------------------------------------------------------
        Scramble::ignoreDefaultRoutes();

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }
}
