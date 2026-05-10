<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain;

use App\Models\User;

final readonly class LogoutAction
{
    public function execute(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function executeAll(User $user): void
    {
        $user->tokens()->delete();
    }
}
