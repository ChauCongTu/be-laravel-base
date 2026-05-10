<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Resources;

use App\Domains\Knowledge\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Note
 */
final class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'folder_id'  => $this->folder_id,
            'folder'     => new FolderResource($this->whenLoaded('folder')),
            'title'      => $this->title,
            'content'    => $this->content,
            'type'       => $this->type,
            'is_pinned'  => $this->is_pinned,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
