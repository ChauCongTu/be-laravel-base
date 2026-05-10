<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'folder_id',
        'title',
        'content',
        'type',
        'is_pinned',
    ];

    protected $casts = [
        'user_id'    => 'integer',
        'folder_id'  => 'integer',
        'is_pinned'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInFolder($query, int $folderId)
    {
        return $query->where('folder_id', $folderId);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeMarkdown($query)
    {
        return $query->where('type', 'markdown');
    }

    public function scopeText($query)
    {
        return $query->where('type', 'text');
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

    public function isMarkdown(): bool
    {
        return $this->type === 'markdown';
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }
}
