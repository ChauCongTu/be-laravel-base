<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function create(array $attributes): User;

    public function update(User $user, array $attributes): User;
}
