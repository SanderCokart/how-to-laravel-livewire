<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    /**
     * Full-page Livewire components (`pages::posts.*`) for interactive HTML UI.
     *
     * Scoped bindings: `{post}` is resolved through `$user->posts()`, so it must belong to that user (404 otherwise).
     *
     * @see https://livewire.laravel.com/docs/4.x/components#page-components Route::livewire()
     * @see https://laravel.com/docs/13.x/routing#implicit-model-binding-scoping Implicit binding — scoping
     */
    Route::scopeBindings()->group(function () {
        Route::livewire('users/{user}/posts', 'pages::posts.index')->name('users.posts.index');
        Route::livewire('users/{user}/posts/{post}', 'pages::posts.show')->name('users.posts.show');
    });
});

require __DIR__.'/settings.php';
