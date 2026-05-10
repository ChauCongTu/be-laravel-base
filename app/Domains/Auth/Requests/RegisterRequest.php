<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use App\Domains\Auth\Data\RegisterData;
use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function toRegisterData(): RegisterData
    {
        return RegisterData::fromArray($this->validated());
    }
}
