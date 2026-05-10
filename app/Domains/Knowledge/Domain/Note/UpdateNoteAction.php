<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Note;

use App\Domains\Knowledge\Data\NoteData;
use App\Domains\Knowledge\Domain\Contracts\NoteRepositoryInterface;
use App\Domains\Knowledge\Exceptions\NoteNotFoundException;
use App\Domains\Knowledge\Models\Note;
use App\Domains\Shared\Exceptions\UnauthorizedException;

final readonly class UpdateNoteAction
{
    public function __construct(
        private NoteRepositoryInterface $repository,
    ) {}

    public function execute(int $noteId, NoteData $data): Note
    {
        $note = $this->repository->findById($noteId);

        if (!$note) {
            throw new NoteNotFoundException($noteId);
        }

        if (!$note->canBeEditedBy($data->userId)) {
            throw new UnauthorizedException('edit this note');
        }

        return $this->repository->update($note, $data);
    }
}
