<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use App\Domains\Auth\Data\RefreshTokenData;
use Illuminate\Foundation\Http\FormRequest;

final class RefreshTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
        ];
    }

    public function toRefreshTokenData(): RefreshTokenData
    {
        return RefreshTokenData::fromArray($this->validated());
    }
}
