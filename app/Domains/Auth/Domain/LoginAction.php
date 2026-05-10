<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain;

use App\Domains\Auth\Data\LoginData;
use App\Domains\Auth\Data\LoginResult;
use App\Domains\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Domains\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;

final readonly class LoginAction
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    public function execute(LoginData $data): LoginResult
    {
        $user = $this->repository->findByEmail($data->email);

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        // Revoke all previous tokens for this device
        $user->tokens()->where('name', $data->deviceName)->delete();

        $token = $user->createToken($data->deviceName)->plainTextToken;

        return new LoginResult(user: $user, token: $token);
    }
}
