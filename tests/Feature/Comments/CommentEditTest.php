<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

function editPayload(array $overrides = []): array
{
    return array_merge([
        'comment' => 'This is an edited comment.',
    ], $overrides);
}

test('an owner can edit their own comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->for($user)->create(['comment' => 'Original text.']);

    $response = $this->actingAs($user)
        ->putJson(route('comment.edit', ['comment' => $comment]), editPayload());

    $response->assertOk()
        ->assertJsonPath('comment', 'This is an edited comment.')
        ->assertJsonPath('user.id', $user->id);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'comment' => 'This is an edited comment.',
    ]);
});

test('a user cannot edit a comment they do not own', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $comment = Comment::factory()->for($post)->for($owner)->create(['comment' => 'Original text.']);

    $this->actingAs($otherUser)
        ->putJson(route('comment.edit', ['comment' => $comment]), editPayload())
        ->assertForbidden();

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'comment' => 'Original text.',
    ]);
});

test('editing a comment shorter than four characters returns 422 and stores nothing', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->for($user)->create(['comment' => 'Original text.']);

    $response = $this->actingAs($user)
        ->putJson(route('comment.edit', ['comment' => $comment]), editPayload(['comment' => 'ab']));

    $response->assertUnprocessable()
        ->assertJsonPath('errors.comment', ['The comment must be at least 4 characters.']);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'comment' => 'Original text.',
    ]);
});

test('editing a comment with unchanged text returns 304', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->for($user)->create(['comment' => 'Original text.']);

    $this->actingAs($user)
        ->putJson(route('comment.edit', ['comment' => $comment]), editPayload(['comment' => 'Original text.']))
        ->assertStatus(304);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'comment' => 'Original text.',
    ]);
});

test('editing a comment without text returns 422 and stores nothing', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->for($user)->create(['comment' => 'Original text.']);

    $response = $this->actingAs($user)
        ->putJson(route('comment.edit', ['comment' => $comment]), []);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.comment', ['The comment field is required.']);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'comment' => 'Original text.',
    ]);
});
