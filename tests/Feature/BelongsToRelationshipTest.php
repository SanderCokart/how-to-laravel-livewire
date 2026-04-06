<?php

/**
 * Feature tests for Eloquent `belongsTo` / `hasMany` behavior (models and queries — no HTTP layer).
 *
 * @see https://laravel.com/docs/13.x/eloquent-relationships Eloquent relationships
 */
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('a post belongs to its user when accessed as a dynamic property', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    expect($post->user)->toBeInstanceOf(User::class)
        ->and($post->user->is($user))->toBeTrue();
});

test('the inverse hasMany returns posts for a user', function () {
    $user = User::factory()->create();
    Post::factory()->count(2)->create(['user_id' => $user->id]);

    expect($user->posts)->toHaveCount(2)
        ->and($user->posts->first())->toBeInstanceOf(Post::class);
});

test('whereBelongsTo scopes posts to the given parent model', function () {
    $authorA = User::factory()->create();
    $authorB = User::factory()->create();
    Post::factory()->create(['user_id' => $authorA->id, 'title' => 'A']);
    Post::factory()->create(['user_id' => $authorB->id, 'title' => 'B']);

    $postsForA = Post::whereBelongsTo($authorA)->get();

    expect($postsForA)->toHaveCount(1)
        ->and($postsForA->first()->title)->toBe('A');
});

test('eager loading user avoids repeated queries when iterating posts', function () {
    Post::factory()->count(3)->create();

    DB::enableQueryLog();

    $posts = Post::with('user')->get();
    foreach ($posts as $post) {
        $post->user->name;
    }

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    // One query for posts, one for all users — not one per post.
    expect(count($queries))->toBe(2);
});

test('deleting the parent user cascades and removes child posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $user->delete();

    expect(Post::query()->whereKey($post->id)->exists())->toBeFalse();
});
