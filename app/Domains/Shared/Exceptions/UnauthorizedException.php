<?php

declare(strict_types=1);

namespace App\Domains\Shared\Exceptions;

use RuntimeException;

final class UnauthorizedException extends RuntimeException
{
    public function __construct(string $action = 'perform this action')
    {
        parent::__construct("You are not authorized to {$action}.");
    }
}
