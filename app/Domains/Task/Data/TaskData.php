<?php

declare(strict_types=1);

namespace App\Domains\Task\Data;

use Carbon\Carbon;

final readonly class TaskData
{
    public function __construct(
        public int $userId,
        public string $title,
        public ?string $description = null,
        public string $status = 'todo',
        public string $priority = 'medium',
        public ?Carbon $dueDate = null,
        public ?Carbon $reminderAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) $data['user_id'],
            title: (string) $data['title'],
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'todo',
            priority: $data['priority'] ?? 'medium',
            dueDate: isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
            reminderAt: isset($data['reminder_at']) ? Carbon::parse($data['reminder_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id'     => $this->userId,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'priority'    => $this->priority,
            'due_date'    => $this->dueDate?->toDateTimeString(),
            'reminder_at' => $this->reminderAt?->toDateTimeString(),
        ];
    }
}
