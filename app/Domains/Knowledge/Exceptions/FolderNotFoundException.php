<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Exceptions;

use RuntimeException;

final class FolderNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Folder with ID {$id} not found.");
    }
}
