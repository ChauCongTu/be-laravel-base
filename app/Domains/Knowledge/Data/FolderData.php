<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Data;

final readonly class FolderData
{
    public function __construct(
        public int $userId,
        public string $name,
        public ?string $color = null,
        public ?string $icon = null,
        public ?int $parentId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) $data['user_id'],
            name: (string) $data['name'],
            color: $data['color'] ?? null,
            icon: $data['icon'] ?? null,
            parentId: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id'   => $this->userId,
            'name'      => $this->name,
            'color'     => $this->color,
            'icon'      => $this->icon,
            'parent_id' => $this->parentId,
        ];
    }
}
