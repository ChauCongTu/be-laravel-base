<?php

declare(strict_types=1);

namespace App\Domains\Task\Resources;

use App\Domains\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
final class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'status'         => $this->status,
            'status_label'   => $this->getStatusLabel(),
            'priority'       => $this->priority,
            'priority_label' => $this->getPriorityLabel(),
            'priority_color' => $this->getPriorityColor(),
            'due_date'       => $this->due_date?->toIso8601String(),
            'reminder_at'    => $this->reminder_at?->toIso8601String(),
            'is_overdue'     => $this->isOverdue(),
            'is_due_today'   => $this->isDueToday(),
            'days_until_due' => $this->getDaysUntilDue(),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
