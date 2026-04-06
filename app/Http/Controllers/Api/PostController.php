<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON API for posts — mirrors the belongs-to / hasMany behaviour of the Livewire UI without returning HTML.
 *
 * These routes are registered from `routes/api.php` via `bootstrap/app.php` → `withRouting(api: ...)`.
 * The route file wraps them in `web` + `auth` + `verified` so session cookies and `actingAs()` work; the outer
 * `api` middleware group is still applied by the framework. For Bearer tokens, use `php artisan install:api`
 * (Sanctum) and `auth:sanctum` instead of `web`.
 *
 * @see https://laravel.com/docs/13.x/eloquent-serialization#serializing-to-json Eloquent JSON responses
 * @see https://laravel.com/docs/13.x/sanctum Sanctum (token APIs)
 */
class PostController extends Controller
{
    /**
     * @return JsonResponse JSON array of posts with nested `user` when eager-loaded.
     */
    public function index(Request $request, User $user): JsonResponse
    {
        abort_unless($request->user()->is($user), 403);

        $posts = $user->posts()
            ->with('user')
            ->latest()
            ->get();

        return response()->json($posts);
    }

    /**
     * @return JsonResponse HTTP 201 with the new post (including `user` relation).
     */
    public function store(StorePostRequest $request, User $user): JsonResponse
    {
        $post = $user->posts()->create($request->validated());

        return response()->json($post->load('user'), 201);
    }

    /**
     * Scoped `{post}` binding ensures the post belongs to `{user}` (404 if not).
     *
     * @return JsonResponse Single post with `user` loaded.
     */
    public function show(Request $request, User $user, Post $post): JsonResponse
    {
        abort_unless($request->user()->is($user), 403);

        return response()->json($post->load('user'));
    }
}
