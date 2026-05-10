<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1\Auth;

use App\Domains\Auth\Domain\LoginAction;
use App\Domains\Auth\Domain\LogoutAction;
use App\Domains\Auth\Domain\RegisterAction;
use App\Domains\Auth\Domain\UploadAvatarAction;
use App\Domains\Auth\Exceptions\InvalidCredentialsException;
use App\Domains\Auth\Requests\LoginRequest;
use App\Domains\Auth\Requests\RegisterRequest;
use App\Domains\Auth\Requests\UpdateProfileRequest;
use App\Domains\Auth\Requests\UploadAvatarRequest;
use App\Domains\Auth\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

final class AuthController
{
    public function __construct(
        private readonly RegisterAction     $registerAction,
        private readonly LoginAction        $loginAction,
        private readonly LogoutAction       $logoutAction,
        private readonly UploadAvatarAction $uploadAvatarAction,
    ) {}

    /** POST /api/v1/auth/register */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user  = $this->registerAction->execute($request->toRegisterData());
        $token = $user->createToken($request->input('device_name', 'api'))->plainTextToken;

        return (new UserResource($user))
            ->additional(['token' => $token, 'token_type' => 'Bearer'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /** POST /api/v1/auth/login */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->loginAction->execute($request->toLoginData());

            return (new UserResource($result->user))
                ->additional(['token' => $result->token, 'token_type' => 'Bearer'])
                ->response();
        } catch (InvalidCredentialsException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    /** GET /api/v1/auth/me */
    public function me(Request $request): JsonResponse
    {
        return (new UserResource($request->user()))->response();
    }

    /** PUT /api/v1/auth/me */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return (new UserResource($request->user()->fresh()))->response();
    }

    /** POST /api/v1/auth/avatar */
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $this->uploadAvatarAction->execute(
            $request->user(),
            $request->file('avatar'),
        );

        return (new UserResource($user))->response();
    }

    /** DELETE /api/v1/auth/avatar */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return (new UserResource($user->fresh()))->response();
    }

    /** PUT /api/v1/auth/password */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->user()
            ->tokens()
            ->where('id', '!=', $request->user()->currentAccessToken()->id)
            ->delete();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /** POST /api/v1/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $this->logoutAction->execute($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /** POST /api/v1/auth/logout-all */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->logoutAction->executeAll($request->user());

        return response()->json(['message' => 'Logged out from all devices successfully.']);
    }
}
