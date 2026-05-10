<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Infrastructure\Repositories;

use App\Domains\Knowledge\Data\SnippetData;
use App\Domains\Knowledge\Domain\Contracts\SnippetRepositoryInterface;
use App\Domains\Knowledge\Models\Snippet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentSnippetRepository implements SnippetRepositoryInterface
{
    public function findById(int $id): ?Snippet
    {
        return Snippet::with('folder')->find($id);
    }

    public function paginateForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Snippet::query()
            ->forUser($userId)
            ->with('folder')
            ->orderByDesc('updated_at');

        if (!empty($filters['folder_id'])) {
            $query->inFolder((int) $filters['folder_id']);
        }

        if (!empty($filters['language'])) {
            $query->byLanguage($filters['language']);
        }

        if (!empty($filters['search'])) {
            $query->whereFullText(['title', 'code_block'], $filters['search']);
        }

        return $query->paginate(20)->withQueryString();
    }

    public function create(SnippetData $data): Snippet
    {
        return Snippet::create($data->toArray());
    }

    public function update(Snippet $snippet, SnippetData $data): Snippet
    {
        $snippet->update([
            'folder_id'   => $data->folderId,
            'title'       => $data->title,
            'code_block'  => $data->codeBlock,
            'language'    => $data->language,
            'description' => $data->description,
        ]);

        return $snippet->fresh();
    }

    public function delete(Snippet $snippet): void
    {
        $snippet->delete();
    }
}
