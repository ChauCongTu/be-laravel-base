<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Folder;

use App\Domains\Knowledge\Domain\Contracts\FolderRepositoryInterface;
use App\Domains\Knowledge\Exceptions\FolderNotFoundException;
use App\Domains\Shared\Exceptions\UnauthorizedException;

final readonly class DeleteFolderAction
{
    public function __construct(
        private FolderRepositoryInterface $repository,
    ) {}

    public function execute(int $folderId, int $userId): void
    {
        $folder = $this->repository->findById($folderId);

        if (!$folder) {
            throw new FolderNotFoundException($folderId);
        }

        if (!$folder->canBeEditedBy($userId)) {
            throw new UnauthorizedException('delete this folder');
        }

        $this->repository->delete($folder);
    }
}
