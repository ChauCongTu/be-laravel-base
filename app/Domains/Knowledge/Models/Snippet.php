<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Snippet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'code_block',
        'language',
        'description',
        'user_id',
        'folder_id',
    ];

    protected $casts = [
        'folder_id'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = ['deleted_at'];

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

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', strtolower($language));
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
}
