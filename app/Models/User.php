<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * Application user (authentication + profile). Demonstrates the **one** side of one-to-many with {@see Post}.
 *
 * **`#[Fillable]` / `#[Hidden]`** — Same mass-assignment idea as {@see Post}: only fillable attributes are
 * set in bulk; hidden attributes are omitted from JSON/array serialization (e.g. API responses).
 *
 * @see https://laravel.com/docs/13.x/eloquent#mass-assignment Mass assignment
 * @see https://laravel.com/docs/13.x/eloquent-serialization#hiding-attributes-from-json Hiding attributes from JSON
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Posts owned by this user (the “one” side of one-to-many; {@see Post::user()} is the inverse `belongsTo`).
     *
     * `hasMany()` declares the relationship from the model that does **not** store the foreign key on its own
     * row — child `posts` rows store `user_id`.
     *
     * @see https://laravel.com/docs/13.x/eloquent-relationships#one-to-many One-to-many / hasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
