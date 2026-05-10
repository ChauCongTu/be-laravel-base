<?php

declare(strict_types=1);

namespace App\Domains\Task\Providers;

use App\Domains\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Domains\Task\Infrastructure\Repositories\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

final class TaskServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TaskRepositoryInterface::class,
            EloquentTaskRepository::class,
        );
    }
}
