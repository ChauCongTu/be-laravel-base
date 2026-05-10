<?php

declare(strict_types=1);

namespace App\Domains\Task\Domain\Contracts;

use App\Domains\Task\Data\TaskData;
use App\Domains\Task\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function findById(int $id): ?Task;

    public function paginateForUser(int $userId, array $filters = []): LengthAwarePaginator;

    public function create(TaskData $data): Task;

    public function update(Task $task, TaskData $data): Task;

    public function delete(Task $task): void;
}
