<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain;

use App\Domains\Auth\Data\RegisterData;
use App\Domains\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class RegisterAction
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    public function execute(RegisterData $data): User
    {
        return $this->repository->create([
            'name'     => $data->name,
            'email'    => $data->email,
            'password' => Hash::make($data->password),
        ]);
    }
}
