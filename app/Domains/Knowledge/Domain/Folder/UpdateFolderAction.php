<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Folder;

use App\Domains\Knowledge\Data\FolderData;
use App\Domains\Knowledge\Domain\Contracts\FolderRepositoryInterface;
use App\Domains\Knowledge\Exceptions\FolderNotFoundException;
use App\Domains\Knowledge\Models\Folder;
use App\Domains\Shared\Exceptions\UnauthorizedException;

final readonly class UpdateFolderAction
{
    public function __construct(
        private FolderRepositoryInterface $repository,
    ) {}

    public function execute(int $folderId, FolderData $data): Folder
    {
        $folder = $this->repository->findById($folderId);

        if (!$folder) {
            throw new FolderNotFoundException($folderId);
        }

        if (!$folder->canBeEditedBy($data->userId)) {
            throw new UnauthorizedException('edit this folder');
        }

        return $this->repository->update($folder, $data);
    }
}
