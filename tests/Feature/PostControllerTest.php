<?php

/**
 * HTTP and Livewire tests for `users/{user}/posts` pages (`pages::posts.index`, `pages::posts.show`).
 *
 * @see https://laravel.com/docs/13.x/http-tests HTTP tests
 * @see https://livewire.laravel.com/docs/4.x/testing Testing Livewire
 * @see https://laravel.com/docs/13.x/routing#implicit-model-binding-scoping Scoped bindings
 */
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected from the posts index', function () {
    $user = User::factory()->create();

    $response = $this->get(route('users.posts.index', $user));

    $response->assertRedirect(route('login'));
});

test('authenticated users can list their own posts', function () {
    $user = User::factory()->create();
    Post::factory()->count(2)->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $response = $this->get(route('users.posts.index', $user));

    $response->assertOk();
});

test('users cannot list another users posts index', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $this->actingAs($other);

    $response = $this->get(route('users.posts.index', $owner));

    $response->assertForbidden();
});

test('users can create a post via Livewire using the hasMany create shortcut', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('pages::posts.index', ['user' => $user])
        ->set('postTitle', 'Scoped binding showcase')
        ->call('createPost')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'title' => 'Scoped binding showcase',
    ]);
});

test('scoped route model binding returns 404 when the post belongs to a different user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $other->id]);
    $this->actingAs($user);

    $response = $this->get(route('users.posts.show', [$user, $post]));

    $response->assertNotFound();
});

test('authenticated users can view their own post show page', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $response = $this->get(route('users.posts.show', [$user, $post]));

    $response->assertOk()->assertSee($post->title, escape: false);
});
