<?php

declare(strict_types=1);

namespace App\Domains\Task\Infrastructure\Repositories;

use App\Domains\Task\Data\TaskData;
use App\Domains\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Domains\Task\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function findById(int $id): ?Task
    {
        return Task::find($id);
    }

    public function paginateForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Task::query()
            ->forUser($userId)
            ->orderByDesc('created_at');

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->byPriority($filters['priority']);
        }

        if (filter_var($filters['overdue'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->overdue();
        }

        if (filter_var($filters['due_today'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->dueToday();
        }

        if (filter_var($filters['due_soon'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->dueSoon();
        }

        return $query->paginate(20)->withQueryString();
    }

    public function create(TaskData $data): Task
    {
        return Task::create($data->toArray());
    }

    public function update(Task $task, TaskData $data): Task
    {
        $task->update([
            'title'       => $data->title,
            'description' => $data->description,
            'status'      => $data->status,
            'priority'    => $data->priority,
            'due_date'    => $data->dueDate,
            'reminder_at' => $data->reminderAt,
        ]);

        return $task->fresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
