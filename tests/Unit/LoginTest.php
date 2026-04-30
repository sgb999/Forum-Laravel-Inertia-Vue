<?php

use App\Models\User;

test('page loads', function () {
    $response = $this->get(route('login.index'));

    $response->assertStatus(200);
});

test('can login', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);

    $response->assertRedirect(route('home'));
});

test('can not login with incorrect password', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password1234',
    ]);

    $response->assertSessionHasErrors();

    $response->assertStatus(302);

    $response->assertRedirectBack();
});


test('can not login with incorrect email', function () {
    User::factory()->create(['email' => 'email1234@email.com', 'password' => bcrypt('password')]);
    $response = $this->post(route('login.post'), [
        'email' => 'email@email.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors();

    $response->assertStatus(302);

    $response->assertRedirectBack();
});

test('can not load login page if user is logged in', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);
    $response = $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $response->assertSessionHasNoErrors();

    $response->assertRedirect();

    $response = $this->get(route('login.index'));
    $response->assertRedirect(route('home'));
});
