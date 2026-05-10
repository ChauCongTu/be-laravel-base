<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Contracts;

use App\Domains\Knowledge\Data\FolderData;
use App\Domains\Knowledge\Models\Folder;
use Illuminate\Database\Eloquent\Collection;

interface FolderRepositoryInterface
{
    public function findById(int $id): ?Folder;

    /** Root folders (no parent) with counts + children eager-loaded */
    public function listForUser(int $userId): Collection;

    /** Single folder with counts + parent + children eager-loaded */
    public function findWithRelations(int $id): ?Folder;

    public function create(FolderData $data): Folder;

    public function update(Folder $folder, FolderData $data): Folder;

    public function delete(Folder $folder): void;
}
