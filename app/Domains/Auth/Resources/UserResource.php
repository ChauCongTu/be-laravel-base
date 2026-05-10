<?php

declare(strict_types=1);

namespace App\Domains\Auth\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'user_name'         => $this->user_name,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),

            // Profile
            'avatar'            => $this->avatarUrl(),
            'phone'             => $this->phone,
            'nationality'       => $this->nationality,
            'city'              => $this->city,
            'address'           => $this->address,
            'gender'            => $this->gender,

            // Preferences
            'timezone'          => $this->timezone,
            'locale'            => $this->locale,
            'theme'             => $this->theme,

            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
