<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Resources;

use App\Domains\Knowledge\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Folder
 */
final class FolderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'color'          => $this->color,
            'icon'           => $this->icon,
            'parent_id'      => $this->parent_id,
            'children'       => FolderResource::collection($this->whenLoaded('children')),
            'notes_count'    => $this->whenCounted('notes'),
            'snippets_count' => $this->whenCounted('snippets'),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
