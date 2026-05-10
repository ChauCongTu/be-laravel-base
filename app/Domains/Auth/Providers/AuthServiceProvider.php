<?php

declare(strict_types=1);

namespace App\Domains\Auth\Providers;

use App\Domains\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Domains\Auth\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class,
        );
    }
}
