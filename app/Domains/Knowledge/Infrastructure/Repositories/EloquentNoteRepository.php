<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Infrastructure\Repositories;

use App\Domains\Knowledge\Data\NoteData;
use App\Domains\Knowledge\Domain\Contracts\NoteRepositoryInterface;
use App\Domains\Knowledge\Models\Note;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentNoteRepository implements NoteRepositoryInterface
{
    public function findById(int $id): ?Note
    {
        return Note::with('folder')->find($id);
    }

    public function paginateForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Note::query()
            ->forUser($userId)
            ->with('folder')
            ->orderByDesc('is_pinned')
            ->orderByDesc('updated_at');

        if (!empty($filters['folder_id'])) {
            $query->inFolder((int) $filters['folder_id']);
        }

        if (filter_var($filters['is_pinned'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->pinned();
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->whereFullText(['title', 'content'], $filters['search']);
        }

        return $query->paginate(20)->withQueryString();
    }

    public function create(NoteData $data): Note
    {
        return Note::create($data->toArray());
    }

    public function update(Note $note, NoteData $data): Note
    {
        $note->update([
            'folder_id' => $data->folderId,
            'title'     => $data->title,
            'content'   => $data->content,
            'type'      => $data->type,
            'is_pinned' => $data->isPinned,
        ]);

        return $note->fresh();
    }

    public function delete(Note $note): void
    {
        $note->delete();
    }
}
