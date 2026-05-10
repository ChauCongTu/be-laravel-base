<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Domain\Snippet;

use App\Domains\Knowledge\Data\SnippetData;
use App\Domains\Knowledge\Domain\Contracts\SnippetRepositoryInterface;
use App\Domains\Knowledge\Exceptions\SnippetNotFoundException;
use App\Domains\Knowledge\Models\Snippet;
use App\Domains\Shared\Exceptions\UnauthorizedException;

final readonly class UpdateSnippetAction
{
    public function __construct(
        private SnippetRepositoryInterface $repository,
    ) {}

    public function execute(int $snippetId, SnippetData $data): Snippet
    {
        $snippet = $this->repository->findById($snippetId);

        if (!$snippet) {
            throw new SnippetNotFoundException($snippetId);
        }

        if (!$snippet->canBeEditedBy($data->userId)) {
            throw new UnauthorizedException('edit this snippet');
        }

        return $this->repository->update($snippet, $data);
    }
}
