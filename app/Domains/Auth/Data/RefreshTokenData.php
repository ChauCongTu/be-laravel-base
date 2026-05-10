<?php

declare(strict_types=1);

namespace App\Domains\Auth\Data;

final readonly class RefreshTokenData
{
    public function __construct(
        public string $refreshToken,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            refreshToken: (string) $data['refresh_token'],
        );
    }
}
