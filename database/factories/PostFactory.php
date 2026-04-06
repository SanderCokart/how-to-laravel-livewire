<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for {@see Post}; used in tests and seeders to generate realistic rows without manual SQL.
 *
 * Assigning `'user_id' => User::factory()` nests factory resolution: when the {@see Post} is created, Laravel
 * persists a related {@see User} first and wires `user_id` — mirroring the real `belongsTo` constraint.
 *
 * @extends Factory<Post>
 *
 * @see https://laravel.com/docs/13.x/eloquent-factories Eloquent factories
 * @see https://laravel.com/docs/13.x/eloquent-factories#defining-relationships Factory relationships
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            // Deferred: a new User is created when the Post is persisted (nested factory).
            'user_id' => User::factory(),
        ];
    }
}
