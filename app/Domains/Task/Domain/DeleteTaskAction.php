<?php

declare(strict_types=1);

namespace App\Domains\Task\Domain;

use App\Domains\Shared\Exceptions\UnauthorizedException;
use App\Domains\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Domains\Task\Exceptions\TaskNotFoundException;

final readonly class DeleteTaskAction
{
    public function __construct(
        private TaskRepositoryInterface $repository,
    ) {}

    public function execute(int $taskId, int $userId): void
    {
        $task = $this->repository->findById($taskId);

        if (!$task) {
            throw new TaskNotFoundException($taskId);
        }

        if (!$task->canBeEditedBy($userId)) {
            throw new UnauthorizedException('delete this task');
        }

        $this->repository->delete($task);
    }
}
