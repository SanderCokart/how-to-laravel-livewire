<?php

/**
 * JSON API tests for {@see PostController} (`/api/users/{user}/posts`).
 *
 * @see https://laravel.com/docs/13.x/http-tests#testing-json-apis Testing JSON APIs
 */
use App\Http\Controllers\Api\PostController;
use App\Models\Post;
use App\Models\User;

test('guests cannot access the posts api index', function () {
    $user = User::factory()->create();

    $response = $this->getJson(route('api.users.posts.index', $user));

    $response->assertUnauthorized();
});

test('authenticated users receive json for their posts', function () {
    $user = User::factory()->create();
    Post::factory()->create(['user_id' => $user->id, 'title' => 'API list item']);
    $this->actingAs($user);

    $response = $this->getJson(route('api.users.posts.index', $user));

    $response->assertOk()
        ->assertJsonPath('0.title', 'API list item')
        ->assertJsonPath('0.user_id', $user->id);
});

test('users can create a post via the api', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('api.users.posts.store', $user), [
        'title' => 'Created via API',
    ]);

    $response->assertCreated()
        ->assertJsonPath('title', 'Created via API')
        ->assertJsonPath('user_id', $user->id);

    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'title' => 'Created via API',
    ]);
});

test('api returns 404 when the post belongs to a different user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $other->id]);
    $this->actingAs($user);

    $response = $this->getJson(route('api.users.posts.show', [$user, $post]));

    $response->assertNotFound();
});

test('users cannot access another users api index', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $this->actingAs($other);

    $response = $this->getJson(route('api.users.posts.index', $owner));

    $response->assertForbidden();
});
