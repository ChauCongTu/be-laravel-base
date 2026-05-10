<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Snippet;

use App\Domains\Knowledge\Domain\Contracts\SnippetRepositoryInterface;
use App\Domains\Knowledge\Exceptions\SnippetNotFoundException;
use App\Domains\Shared\Exceptions\UnauthorizedException;

final readonly class DeleteSnippetAction
{
    public function __construct(
        private SnippetRepositoryInterface $repository,
    ) {}

    public function execute(int $snippetId, int $userId): void
    {
        $snippet = $this->repository->findById($snippetId);

        if (!$snippet) {
            throw new SnippetNotFoundException($snippetId);
        }

        if (!$snippet->canBeEditedBy($userId)) {
            throw new UnauthorizedException('delete this snippet');
        }

        $this->repository->delete($snippet);
    }
}
