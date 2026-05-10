<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Folder;

use App\Domains\Knowledge\Data\FolderData;
use App\Domains\Knowledge\Domain\Contracts\FolderRepositoryInterface;
use App\Domains\Knowledge\Models\Folder;

final readonly class CreateFolderAction
{
    public function __construct(
        private FolderRepositoryInterface $repository,
    ) {}

    public function execute(FolderData $data): Folder
    {
        return $this->repository->create($data);
    }
}
