<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Repositories;

use App\Domains\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Models\User;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user->fresh();
    }
}
