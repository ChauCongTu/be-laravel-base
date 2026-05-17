<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1\Auth;

use App\Domains\Auth\Domain\LoginAction;
use App\Domains\Auth\Domain\LogoutAction;
use App\Domains\Auth\Domain\RefreshTokenAction;
use App\Domains\Auth\Domain\RegisterAction;
use App\Domains\Auth\Domain\UploadAvatarAction;
use App\Domains\Auth\Exceptions\InvalidCredentialsException;
use App\Domains\Auth\Exceptions\InvalidRefreshTokenException;
use App\Domains\Auth\Requests\LoginRequest;
use App\Domains\Auth\Requests\RefreshTokenRequest;
use App\Domains\Auth\Requests\RegisterRequest;
use App\Domains\Auth\Requests\UpdateProfileRequest;
use App\Domains\Auth\Requests\UploadAvatarRequest;
use App\Domains\Auth\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

final class AuthController
{
    public function __construct(
        private readonly RegisterAction      $registerAction,
        private readonly LoginAction         $loginAction,
        private readonly LogoutAction        $logoutAction,
        private readonly RefreshTokenAction  $refreshTokenAction,
        private readonly UploadAvatarAction  $uploadAvatarAction,
    ) {}

    // =========================================================================
    // Public
    // =========================================================================

    /**
     * POST /api/v1/auth/register
     *
     * Register a new account and issue an initial token pair.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->registerAction->execute($request->toRegisterData());

        // Issue tokens via Password Grant immediately after registration
        $loginResult = $this->loginAction->execute(
            \App\Domains\Auth\Data\LoginData::fromArray([
                'email'    => $request->email,
                'password' => $request->input('password'),
            ])
        );

        return (new UserResource($loginResult->user))
            ->additional($this->tokenPayload($loginResult))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * POST /api/v1/auth/login
     *
     * Authenticate and receive access_token + refresh_token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->loginAction->execute($request->toLoginData());

            return (new UserResource($result->user))
                ->additional($this->tokenPayload($result))
                ->response();
        } catch (InvalidCredentialsException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * POST /api/v1/auth/refresh
     *
     * Exchange a valid refresh_token for a new token pair.
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $result = $this->refreshTokenAction->execute($request->toRefreshTokenData());

            return (new UserResource($result->user))
                ->additional($this->tokenPayload($result))
                ->response();
        } catch (InvalidRefreshTokenException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    // =========================================================================
    // Authenticated
    // =========================================================================

    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        return (new UserResource($request->user()))->response();
    }

    /**
     * PUT /api/v1/auth/me
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return (new UserResource($request->user()->fresh()))->response();
    }

    /**
     * POST /api/v1/auth/avatar
     */
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $user = $this->uploadAvatarAction->execute(
            $request->user(),
            $request->file('avatar'),
        );

        return (new UserResource($user))->response();
    }

    /**
     * DELETE /api/v1/auth/avatar
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return (new UserResource($user->fresh()))->response();
    }

    /**
     * PUT /api/v1/auth/password
     *
     * Change password and revoke all other tokens (force re-login on other devices).
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Revoke all tokens except the current one
        $currentTokenId = $request->user()->token()->id;

        foreach ($request->user()->tokens as $token) {
            if ($token->id !== $currentTokenId) {
                $token->revoke();
                $token->refreshTokens()->update(['revoked' => true]);
            }
        }

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /**
     * POST /api/v1/auth/logout
     *
     * Revoke the current access token and its refresh token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->logoutAction->execute($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * POST /api/v1/auth/logout-all
     *
     * Revoke all tokens across all devices.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->logoutAction->executeAll($request->user());

        return response()->json(['message' => 'Logged out from all devices successfully.']);
    }

    /**
     * GET /api/v1/auth/users
     * 
     */
    public function index()
    {
        $users = User::get();

        return UserResource::collection($users);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function tokenPayload(\App\Domains\Auth\Data\LoginResult $result): array
    {
        return [
            'token_type'    => 'Bearer',
            'access_token'  => $result->accessToken,
            'refresh_token' => $result->refreshToken,
            'expires_in'    => $result->expiresIn,
        ];
    }
}
