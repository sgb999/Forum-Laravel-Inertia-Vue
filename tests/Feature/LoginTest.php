<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function loginPayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'ada@example.com',
        'password' => 'password',
    ], $overrides);
}

test('the login page renders for guests', function () {
    $this->get(route('login.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('user/Login'));
});

test('a user can log in and is redirected to the posts page', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $this->post(route('login.post'), loginPayload(['email' => $user->email]))
        ->assertSessionHasNoErrors()
        ->assertRedirectToRoute('post.show');

    $this->assertAuthenticatedAs($user);
});

test('login is rejected when the email or password does not match a user', function (string $invalidField) {
    $user = User::factory()->create([
        'email' => 'ada@example.com',
        'password' => bcrypt('password'),
    ]);

    $payload = $invalidField === 'email'
        ? loginPayload(['email' => 'nobody@example.com'])
        : loginPayload(['email' => $user->email, 'password' => 'wrong-password']);

    $this->post(route('login.post'), $payload)
        ->assertSessionHasErrors(['email' => 'The provided credentials do not match our records.'])
        ->assertRedirectBack();

    $this->assertGuest();
})->with(['email', 'password']);

test('login requires an email and a password', function () {
    $this->post(route('login.post'))
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

test('authenticated users are redirected away from the login page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('login.index'))
        ->assertRedirectToRoute('post.show');
});
