<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain;

use App\Models\User;

final readonly class LogoutAction
{
    /**
     * Revoke the current access token (and its associated refresh token).
     */
    public function execute(User $user): void
    {
        $token = $user->token();

        if ($token) {
            // Revoke the access token
            $token->revoke();

            // Revoke the associated refresh token
            $token->refreshTokens()->update(['revoked' => true]);
        }
    }

    /**
     * Revoke all access tokens and refresh tokens for the user.
     */
    public function executeAll(User $user): void
    {
        foreach ($user->tokens as $token) {
            $token->revoke();
            $token->refreshTokens()->update(['revoked' => true]);
        }
    }
}
