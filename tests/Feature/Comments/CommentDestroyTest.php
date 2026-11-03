<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

test('an owner can delete their own comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->for($user)->create(['comment' => 'This comment will be deleted.']);

    $this->actingAs($user)
        ->from('/view-post/'.$post->id)
        ->delete(route('comment.destroy', ['comment' => $comment]))
        ->assertRedirect('/view-post/'.$post->id);

    $this->assertDatabaseMissing(Comment::class, [
        'id' => $comment->id,
    ]);
});

test('a user cannot delete a comment they do not own', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $comment = Comment::factory()->for($post)->for($owner)->create(['comment' => 'This comment will not be deleted.']);

    $this->actingAs($otherUser)
        ->from('/view-post/'.$post->id)
        ->delete(route('comment.destroy', ['comment' => $comment]))
        ->assertForbidden();

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'comment' => 'This comment will not be deleted.',
    ]);
});
