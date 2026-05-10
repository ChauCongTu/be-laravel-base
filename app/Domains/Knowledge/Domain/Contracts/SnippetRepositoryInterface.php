<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Contracts;

use App\Domains\Knowledge\Data\SnippetData;
use App\Domains\Knowledge\Models\Snippet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SnippetRepositoryInterface
{
    public function findById(int $id): ?Snippet;

    public function paginateForUser(int $userId, array $filters = []): LengthAwarePaginator;

    public function create(SnippetData $data): Snippet;

    public function update(Snippet $snippet, SnippetData $data): Snippet;

    public function delete(Snippet $snippet): void;
}
