<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Full-page Livewire UI for listing and creating posts under `users/{user}/posts`.
 *
 * Replaces a classic controller + Blade form with `wire:submit`, validation in the component, and reactive UI.
 *
 * @see https://livewire.laravel.com/docs/4.x/components Page components
 * @see https://laravel.com/docs/13.x/eloquent-relationships Eloquent relationships
 */
new #[Title('Posts')] class extends Component
{
    /** Route model binding resolves the profile owner; only that user may view this page. */
    public User $user;

    /** New post title (maps to `posts.title` on save). */
    public string $postTitle = '';

    /**
     * Loaded posts for the list (serialized between Livewire requests; refreshed in {@see loadPosts()}).
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, Post>
     */
    public $posts;

    public function mount(User $user): void
    {
        abort_unless(Auth::user()?->is($user), 403);

        $this->user = $user;
        $this->loadPosts();
    }

    /**
     * Loads posts for this user, eager-loading `user` for each row (belongsTo author).
     */
    public function loadPosts(): void
    {
        $this->posts = $this->user->posts()->with('user')->latest()->get();
    }

    /**
     * Validates input and creates a row via the parent `hasMany` (sets `user_id` automatically).
     */
    public function createPost(): void
    {
        $validated = $this->validate([
            'postTitle' => ['required', 'string', 'max:255'],
        ]);

        $this->user->posts()->create([
            'title' => $validated['postTitle'],
        ]);

        $this->reset('postTitle');
        $this->loadPosts();
    }
}; ?>

<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="lg">{{ __('Posts') }}</flux:heading>
        <flux:subheading>{{ $user->name }}</flux:subheading>
    </div>

    <form wire:submit="createPost" class="flex max-w-md flex-col gap-4">
        <flux:field>
            <flux:label for="post-title">{{ __('Title') }}</flux:label>
            <flux:input
                id="post-title"
                wire:model="postTitle"
                type="text"
                required
                :placeholder="__('Write a title…')"
            />
            <flux:error name="postTitle" />
        </flux:field>

        <div class="flex items-center gap-3">
            <flux:button type="submit" variant="primary" data-test="create-post-submit">
                <span wire:loading.remove wire:target="createPost">{{ __('Create post') }}</span>
                <span wire:loading wire:target="createPost">{{ __('Saving…') }}</span>
            </flux:button>
        </div>
    </form>

    <flux:separator />

    <ul class="flex flex-col gap-2">
        @forelse ($posts as $post)
            <li wire:key="post-{{ $post->id }}">
                <flux:link :href="route('users.posts.show', [$user, $post])" wire:navigate class="font-medium">
                    {{ $post->title }}
                </flux:link>
            </li>
        @empty
            <flux:text class="text-zinc-500">{{ __('No posts yet.') }}</flux:text>
        @endforelse
    </ul>
</div>
