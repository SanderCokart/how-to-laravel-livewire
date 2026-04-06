<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Blog-style post that belongs to a {@see User} (foreign key `user_id`).
 *
 * **Mass assignment (`#[Fillable]`)** — Only attributes listed here may be set via `create()`, `fill()`,
 * `update()`, and `$user->posts()->create([...])`. Anything not listed is ignored for mass assignment,
 * which limits accidental or malicious bulk writes (e.g. from request input).
 *
 * The legacy equivalent is `$fillable` on the model class; `$guarded` is the inverse (block list).
 *
 * @see https://laravel.com/docs/13.x/eloquent#mass-assignment Mass assignment, `Fillable`, and `Guarded`
 */
#[Fillable(['title', 'user_id'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * The user who authored this post (inverse of {@see User::posts()} — the “many” side of one-to-many).
     *
     * `belongsTo()` means this model **owns the foreign key** (`user_id`); the parent is the related `User`.
     * Eloquent infers the FK column as `user_id` from the method name `user` + `_id`; the owner key defaults
     * to `users.id`.
     *
     * @see https://laravel.com/docs/13.x/eloquent-relationships#one-to-many-inverse One-to-many (inverse) / belongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);

        // Explicit keys when you cannot follow conventions:
        // return $this->belongsTo(User::class, 'author_user_id', 'id');
    }
}
