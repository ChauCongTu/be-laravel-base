<?php

declare(strict_types=1);

namespace App\Domains\Task\Domain;

use App\Domains\Shared\Exceptions\UnauthorizedException;
use App\Domains\Task\Data\TaskData;
use App\Domains\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Domains\Task\Exceptions\TaskNotFoundException;
use App\Domains\Task\Models\Task;

final readonly class UpdateTaskAction
{
    public function __construct(
        private TaskRepositoryInterface $repository,
    ) {}

    public function execute(int $taskId, TaskData $data): Task
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new TaskNotFoundException($taskId);
        }

        if (!$task->canBeEditedBy($data->userId)) {
            throw new UnauthorizedException('edit this task');
        }

        return $this->repository->update($task, $data);
    }
}
