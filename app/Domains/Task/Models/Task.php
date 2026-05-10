<?php

declare(strict_types=1);

namespace App\Domains\Task\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'reminder_at',
    ];

    protected $casts = [
        'user_id'     => 'integer',
        'due_date'    => 'datetime',
        'reminder_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'done');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->toDateString());
    }

    public function scopeDueSoon($query)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays(3)])
            ->where('status', '!=', 'done');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['todo', 'doing']);
    }

    // -------------------------------------------------------------------------
    // Business rules
    // -------------------------------------------------------------------------

    public function canBeEditedBy(?int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function canBeViewedBy(?int $userId): bool
    {
        return $this->user_id === $userId;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }

    public function isDueToday(): bool
    {
        return $this->due_date && $this->due_date->isToday();
    }

    public function isDueSoon(): bool
    {
        return $this->due_date
            && $this->due_date->isFuture()
            && $this->due_date->lte(now()->addDays(3));
    }

    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'todo'  => 'To Do',
            'doing' => 'In Progress',
            'done'  => 'Completed',
            default => $this->status,
        };
    }

    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'low'    => 'Low',
            'medium' => 'Medium',
            'high'   => 'High',
            default  => $this->priority,
        };
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            'low'    => '#4ade80',
            'medium' => '#facc15',
            'high'   => '#f87171',
            default  => '#9ca3af',
        };
    }

    public function getDaysUntilDue(): ?float
    {
        if (!$this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }
}
