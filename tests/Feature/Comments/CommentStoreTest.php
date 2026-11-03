<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

function commentPayload(array $overrides = []): array
{
    return array_merge([
        'comment' => 'This is a comment.',
    ], $overrides);
}

test('an authenticated user can create a comment on a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->postJson(route('comment.store', ['post' => $post]), commentPayload());

    $response->assertCreated()
        ->assertJsonPath('comment', 'This is a comment.')
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.username', $user->username);

    $this->assertDatabaseHas(Comment::class, [
        'comment' => 'This is a comment.',
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
});

test('a user cannot forge the author of their own comment', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($otherUser)->create();

    $response = $this->actingAs($user)
        ->postJson(route('comment.store', ['post' => $post]), commentPayload([
            'user_id' => $otherUser->id,
        ]));

    $response->assertCreated();

    $this->assertDatabaseHas(Comment::class, [
        'comment' => 'This is a comment.',
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
});

test('a guest receives 401 when trying to create a comment', function () {
    $post = Post::factory()->create();

    $this->postJson(route('comment.store', ['post' => $post]), commentPayload())
        ->assertUnauthorized();
});

test('creating a comment without text returns 422 and stores nothing', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->postJson(route('comment.store', ['post' => $post]), []);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.comment', ['The comment field is required.']);

    $this->assertDatabaseCount(Comment::class, 0);
});
