<?php

declare(strict_types=1);

namespace App\Domains\Auth\Exceptions;

use RuntimeException;

final class InvalidRefreshTokenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The refresh token is invalid or has expired.');
    }
}
