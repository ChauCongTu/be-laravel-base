<?php

declare(strict_types=1);

namespace App\Domains\Task\Domain;

use App\Domains\Task\Data\TaskData;
use App\Domains\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Domains\Task\Models\Task;

final readonly class CreateTaskAction
{
    public function __construct(
        private TaskRepositoryInterface $repository,
    ) {}

    public function execute(TaskData $data): Task
    {
        return $this->repository->create($data);
    }
}
