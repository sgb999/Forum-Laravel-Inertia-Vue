<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

test('a guest does not see the comment form on a post page', function () {
    $post = Post::factory()->create();

    visit('/view-post/'.$post->id)
        ->assertSee('Comments')
        ->assertDontSee('Add a Comment')
        ->assertPathIs('/view-post/'.$post->id)
        ->assertNoJavaScriptErrors();
});

test('the submit button stays disabled until the comment has at least four characters', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/');

    visit('/view-post/'.$post->id)
        ->assertSee('Add a Comment')
        ->assertButtonDisabled('Submit')
        ->fill('new-comment', 'ab')
        ->assertButtonDisabled('Submit')
        ->fill('new-comment', 'abcd')
        ->assertButtonEnabled('Submit');
});

test('a logged in user can create a comment on a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/');

    $page = visit('/view-post/'.$post->id)
        ->assertSee('Add a Comment')
        ->fill('new-comment', 'This is my first comment!')
        ->click('Submit')
        ->assertSee('Your comment has been posted!')
        ->assertSee('This is my first comment!')
        ->assertSee($user->username)
        ->assertPathIs('/view-post/'.$post->id)
        ->assertNoJavaScriptErrors();

    expect($page->value('new-comment'))->toBe('');

    $this->assertDatabaseHas(Comment::class, [
        'comment' => 'This is my first comment!',
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
});

test('an owner can edit their own comment on a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->for($user)->create(['comment' => 'Original comment text']);

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/');

    visit('/view-post/'.$post->id)
        ->assertSee($comment->comment)
        ->click('button#edit')
        ->fill('edit-comment', 'Updated comment text')
        ->click('button#update')
        ->assertSee('Your comment has been updated!')
        ->assertSee('Updated comment text')
        ->assertDontSee('Original comment text')
        ->assertPathIs('/view-post/'.$post->id)
        ->assertNoJavaScriptErrors();

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'comment' => 'Updated comment text',
    ]);
});

test('an owner can delete their own comment on a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $comment = Comment::factory()->for($post)->for($user)->create(['comment' => 'This comment will be deleted']);

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/');

    visit('/view-post/'.$post->id)
        ->assertSee($comment->comment)
        ->click('#delete')
        ->click('.swal2-confirm')
        ->assertSee('Your comment has been Deleted!')
        ->assertDontSee($comment->comment)
        ->assertPathIs('/view-post/'.$post->id)
        ->assertNoJavaScriptErrors();

    $this->assertDatabaseMissing(Comment::class, [
        'id' => $comment->id,
    ]);
});

test('a user who does not own a comment cannot edit or delete it', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $comment = Comment::factory()->for($post)->for($otherUser)->create(['comment' => 'A comment by someone else']);

    visit('/login')
        ->fill('email', $owner->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/');

    visit('/view-post/'.$post->id)
        ->assertSee($comment->comment)
        ->assertDontSee('Edit Comment')
        ->assertDontSee('Delete Comment')
        ->assertNoJavaScriptErrors();
});
