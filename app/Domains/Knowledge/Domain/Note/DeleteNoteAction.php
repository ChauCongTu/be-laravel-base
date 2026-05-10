<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Note;

use App\Domains\Knowledge\Domain\Contracts\NoteRepositoryInterface;
use App\Domains\Knowledge\Exceptions\NoteNotFoundException;
use App\Domains\Shared\Exceptions\UnauthorizedException;

final readonly class DeleteNoteAction
{
    public function __construct(
        private NoteRepositoryInterface $repository,
    ) {}

    public function execute(int $noteId, int $userId): void
    {
        $note = $this->repository->findById($noteId);

        if (!$note) {
            throw new NoteNotFoundException($noteId);
        }

        if (!$note->canBeEditedBy($userId)) {
            throw new UnauthorizedException('delete this note');
        }

        $this->repository->delete($note);
    }
}
