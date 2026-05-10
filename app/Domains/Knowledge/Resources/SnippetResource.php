<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Resources;

use App\Domains\Knowledge\Models\Snippet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Snippet
 */
final class SnippetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'folder_id'   => $this->folder_id,
            'folder'      => new FolderResource($this->whenLoaded('folder')),
            'title'       => $this->title,
            'code_block'  => $this->code_block,
            'language'    => $this->language,
            'description' => $this->description,
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
