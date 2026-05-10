<?php

declare(strict_types=1);

namespace App\Domains\Auth\Data;

final readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
        public string $deviceName = 'api',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: (string) $data['email'],
            password: (string) $data['password'],
            deviceName: $data['device_name'] ?? 'api',
        );
    }
}
