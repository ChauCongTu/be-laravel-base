<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Snippet;

use App\Domains\Knowledge\Data\SnippetData;
use App\Domains\Knowledge\Domain\Contracts\SnippetRepositoryInterface;
use App\Domains\Knowledge\Models\Snippet;

final readonly class CreateSnippetAction
{
    public function __construct(
        private SnippetRepositoryInterface $repository,
    ) {}

    public function execute(SnippetData $data): Snippet
    {
        return $this->repository->create($data);
    }
}
