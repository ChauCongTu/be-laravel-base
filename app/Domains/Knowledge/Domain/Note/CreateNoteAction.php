<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Note;

use App\Domains\Knowledge\Data\NoteData;
use App\Domains\Knowledge\Domain\Contracts\NoteRepositoryInterface;
use App\Domains\Knowledge\Models\Note;

final readonly class CreateNoteAction
{
    public function __construct(
        private NoteRepositoryInterface $repository,
    ) {}

    public function execute(NoteData $data): Note
    {
        return $this->repository->create($data);
    }
}
