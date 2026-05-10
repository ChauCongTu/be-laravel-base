<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Contracts;

use App\Domains\Knowledge\Data\NoteData;
use App\Domains\Knowledge\Models\Note;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NoteRepositoryInterface
{
    public function findById(int $id): ?Note;

    public function paginateForUser(int $userId, array $filters = []): LengthAwarePaginator;

    public function create(NoteData $data): Note;

    public function update(Note $note, NoteData $data): Note;

    public function delete(Note $note): void;
}
