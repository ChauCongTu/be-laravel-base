<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'        => ['sometimes', 'string', 'max:255'],
            'user_name'   => ['sometimes', 'string', 'max:50', 'alpha_dash',
                              Rule::unique('users', 'user_name')->ignore($userId)],
            'email'       => ['sometimes', 'string', 'email', 'max:255',
                              Rule::unique('users', 'email')->ignore($userId)],
            'phone'       => ['sometimes', 'nullable', 'string', 'max:20'],
            'nationality' => ['sometimes', 'nullable', 'string', 'max:100'],
            'city'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'address'     => ['sometimes', 'nullable', 'string', 'max:500'],
            'gender'      => ['sometimes', 'nullable',
                              Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
        ];
    }
}
