<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

/**
 * Registered via `bootstrap/app.php` → `withRouting(api: ...)` with the `api` prefix and `api` middleware.
 *
 * `web` is included so session/cookie auth matches the rest of the app; `actingAs()` works in tests.
 * For a token-only API, use `php artisan install:api` (Sanctum) and `auth:sanctum` instead of `web`.
 *
 * @see https://laravel.com/docs/13.x/routing#api-routes API routes
 */
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::scopeBindings()->group(function () {
        Route::get('users/{user}/posts', [PostController::class, 'index'])->name('api.users.posts.index');
        Route::post('users/{user}/posts', [PostController::class, 'store'])->name('api.users.posts.store');
        Route::get('users/{user}/posts/{post}', [PostController::class, 'show'])->name('api.users.posts.show');
    });
});
