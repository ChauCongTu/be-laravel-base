<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Exceptions;

use RuntimeException;

final class SnippetNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Snippet with ID {$id} not found.");
    }
}
