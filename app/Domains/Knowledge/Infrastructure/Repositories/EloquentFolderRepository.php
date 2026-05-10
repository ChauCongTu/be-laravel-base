<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Infrastructure\Repositories;

use App\Domains\Knowledge\Data\FolderData;
use App\Domains\Knowledge\Domain\Contracts\FolderRepositoryInterface;
use App\Domains\Knowledge\Models\Folder;
use Illuminate\Database\Eloquent\Collection;

final class EloquentFolderRepository implements FolderRepositoryInterface
{
    public function findById(int $id): ?Folder
    {
        return Folder::find($id);
    }

    public function listForUser(int $userId): Collection
    {
        return Folder::query()
            ->forUser($userId)
            ->root()
            ->withCount(['notes', 'snippets'])
            ->with('children')
            ->orderBy('name')
            ->get();
    }

    public function findWithRelations(int $id): ?Folder
    {
        return Folder::withCount(['notes', 'snippets'])
            ->with('children', 'parent')
            ->find($id);
    }

    public function create(FolderData $data): Folder
    {
        return Folder::create($data->toArray());
    }

    public function update(Folder $folder, FolderData $data): Folder
    {
        $folder->update([
            'name'      => $data->name,
            'color'     => $data->color,
            'icon'      => $data->icon,
            'parent_id' => $data->parentId,
        ]);

        return $folder->fresh();
    }

    public function delete(Folder $folder): void
    {
        $folder->delete();
    }
}
