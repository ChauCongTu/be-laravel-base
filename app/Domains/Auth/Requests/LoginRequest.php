<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use App\Domains\Auth\Data\LoginData;
use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'       => ['required', 'string', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toLoginData(): LoginData
    {
        return LoginData::fromArray($this->validated());
    }
}
