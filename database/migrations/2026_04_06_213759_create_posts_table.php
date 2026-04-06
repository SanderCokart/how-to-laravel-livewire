<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates `posts` with a foreign key to `users`, matching Eloquent’s `belongsTo` / `hasMany` pairing.
 *
 * @see https://laravel.com/docs/13.x/migrations Migrations
 * @see https://laravel.com/docs/13.x/migrations#foreign-key-constraints Foreign keys (`foreignId`, `constrained`)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('user_id') // FK column — Laravel’s convention for a user() belongsTo is user_id.
                ->constrained() // references users.id and adds a foreign key constraint at the database level.
                ->cascadeOnDelete(); // when the parent User is deleted, their posts are removed automatically.
            $table->timestamps();

            // Alternative: nullable author for drafts — $table->foreignId('user_id')->nullable()->constrained();
            // Alternative: restrict delete — use ->restrictOnDelete() so deleting a user fails if posts exist.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
