<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Data;

final readonly class NoteData
{
    public function __construct(
        public int $userId,
        public string $title,
        public string $content,
        public string $type = 'markdown',
        public bool $isPinned = false,
        public ?int $folderId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) $data['user_id'],
            title: (string) $data['title'],
            content: (string) $data['content'],
            type: $data['type'] ?? 'markdown',
            isPinned: (bool) ($data['is_pinned'] ?? false),
            folderId: isset($data['folder_id']) ? (int) $data['folder_id'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id'   => $this->userId,
            'folder_id' => $this->folderId,
            'title'     => $this->title,
            'content'   => $this->content,
            'type'      => $this->type,
            'is_pinned' => $this->isPinned,
        ];
    }
}
