<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Providers;

use App\Domains\Knowledge\Domain\Contracts\FolderRepositoryInterface;
use App\Domains\Knowledge\Domain\Contracts\NoteRepositoryInterface;
use App\Domains\Knowledge\Domain\Contracts\SnippetRepositoryInterface;
use App\Domains\Knowledge\Infrastructure\Repositories\EloquentFolderRepository;
use App\Domains\Knowledge\Infrastructure\Repositories\EloquentNoteRepository;
use App\Domains\Knowledge\Infrastructure\Repositories\EloquentSnippetRepository;
use Illuminate\Support\ServiceProvider;

final class KnowledgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FolderRepositoryInterface::class,
            EloquentFolderRepository::class,
        );

        $this->app->bind(
            NoteRepositoryInterface::class,
            EloquentNoteRepository::class,
        );

        $this->app->bind(
            SnippetRepositoryInterface::class,
            EloquentSnippetRepository::class,
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../../Infrastructure/Database/Migrations'
        );
    }
}
