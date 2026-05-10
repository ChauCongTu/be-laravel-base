<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final readonly class UploadAvatarAction
{
    /**
     * Store the uploaded avatar, delete the old one, and persist the path.
     *
     * Files are stored under storage/app/public/avatars/{user_id}/
     * and served via the `public` disk (symlinked with `php artisan storage:link`).
     */
    public function execute(User $user, UploadedFile $file): User
    {
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $file->store("avatars/{$user->id}", 'public');

        $user->update(['avatar' => $path]);

        return $user->fresh();
    }
}
