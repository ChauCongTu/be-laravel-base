<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Data;

final readonly class SnippetData
{
    public function __construct(
        public string $title,
        public string $codeBlock,
        public string $language,
        public ?string $description = null,
        public ?int $userId = null,
        public ?int $folderId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: (string) $data['title'],
            codeBlock: (string) $data['code_block'],
            language: (string) $data['language'],
            description: $data['description'] ?? null,
            userId: $data['user_id'] ?? null,
            folderId: $data['folder_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'title'       => $this->title,
            'code_block'  => $this->codeBlock,
            'language'    => $this->language,
            'description' => $this->description,
            'user_id'     => $this->userId,
            'folder_id'   => $this->folderId,
        ];
    }
}
