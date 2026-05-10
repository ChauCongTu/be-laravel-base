<?php

declare(strict_types=1);

namespace App\Domains\Task\Exceptions;

use RuntimeException;

final class TaskNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Task with ID {$id} not found.");
    }
}
