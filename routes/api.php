<?php

declare(strict_types=1);

use App\Domains\Shared\Infrastructure\Middleware\VerifyApiDocumentationAccess;
use App\Presentation\Controllers\Api\V1\Auth\AuthController;
use App\Presentation\Controllers\Api\V1\Knowledge\FolderController;
use App\Presentation\Controllers\Api\V1\Knowledge\NoteController;
use App\Presentation\Controllers\Api\V1\Knowledge\SnippetController;
use App\Presentation\Controllers\Api\V1\Task\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - LifeOS Knowledge & Task Workspace
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function (): void {

    // -------------------------------------------------------------------------
    // Auth — Public endpoints
    // -------------------------------------------------------------------------
    Route::prefix('auth')->name('auth.')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login',    [AuthController::class, 'login'])->name('login');
        Route::post('refresh',  [AuthController::class, 'refresh'])->name('refresh');
    });

    // -------------------------------------------------------------------------
    // API Documentation (HMAC-signed, served from storage)
    // -------------------------------------------------------------------------
    Route::middleware(VerifyApiDocumentationAccess::class)
        ->get('documentation/openapi.json', function () {
            $path = storage_path('api-docs/openapi.json');

            if (!file_exists($path)) {
                abort(404, 'OpenAPI documentation not found. Run: php artisan api:export');
            }

            return response()->file($path, [
                'Content-Type'  => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        })->name('api.docs.openapi');

    // -------------------------------------------------------------------------
    // Protected endpoints — Passport OAuth2
    // -------------------------------------------------------------------------
    Route::middleware('auth:api')->group(function (): void {

        // Auth — Authenticated endpoints
        Route::prefix('auth')->name('auth.')->group(function (): void {
            Route::get('me',           [AuthController::class, 'me'])->name('me');
            Route::put('me',           [AuthController::class, 'updateProfile'])->name('update-profile');
            Route::post('avatar',      [AuthController::class, 'uploadAvatar'])->name('upload-avatar');
            Route::delete('avatar',    [AuthController::class, 'deleteAvatar'])->name('delete-avatar');
            Route::put('password',     [AuthController::class, 'changePassword'])->name('change-password');
            Route::post('logout',      [AuthController::class, 'logout'])->name('logout');
            Route::post('logout-all',  [AuthController::class, 'logoutAll'])->name('logout-all');
            Route::get("/users", [AuthController::class, 'index'])->name('users.index');
        });

        // Knowledge: Folders
        Route::apiResource('folders', FolderController::class);

        // Knowledge: Notes
        Route::apiResource('notes', NoteController::class);

        // Knowledge: Snippets
        Route::apiResource('snippets', SnippetController::class);

        // Task Workspace: Tasks
        Route::apiResource('tasks', TaskController::class);
    });
});
