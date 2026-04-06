<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Full-page Livewire view for a single post under scoped `users/{user}/posts/{post}`.
 *
 * Laravel resolves `{post}` via `$user->posts()` when the route group uses `scopeBindings()` (see `routes/web.php`).
 *
 * @see https://livewire.laravel.com/docs/4.x/components Page components
 * @see https://laravel.com/docs/13.x/routing#implicit-model-binding-scoping Scoped binding
 */
new class extends Component
{
    public User $user;

    public Post $post;

    public function mount(User $user, Post $post): void
    {
        abort_unless(Auth::user()?->is($user), 403);

        $this->user = $user;
        $this->post = $post;
    }

    /**
     * Sets the document title from the post (see Livewire “Dynamic titles”).
     *
     * @see https://livewire.laravel.com/docs/4.x/attribute-title#dynamic-titles
     */
    public function render(): mixed
    {
        return $this->view()
            ->title($this->post->title);
    }
}; ?>

<div class="flex flex-col gap-6">
    <flux:link :href="route('users.posts.index', $user)" wire:navigate class="w-fit text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Back to posts') }}
    </flux:link>

    <article class="flex flex-col gap-2">
        <flux:heading size="xl">{{ $post->title }}</flux:heading>
        <flux:text>
            {{ __('Author') }}:
            <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $post->user->name }}</span>
        </flux:text>
    </article>
</div>
