<?php

use App\Models\User;

test('Request Validation works', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->post(route('login.post'), ['email' => 'test', 'password' => 'password']);

    $response->assertSessionHasErrors([
        'email'
    ]);

    $response = $this->post(route('login.post'), ['email' => 1546, 'password' => 'password']);

    $response->assertSessionHasErrors(['email']);

    $response = $this->post(route('login.post'), ['password' => 'password']);

    $response->assertSessionHasErrors(['email']);

    $response = $this->post(route('login.post'), ['email' => 'email']);

    $response->assertSessionHasErrors(['password']);

    $response = $this->post(route('login.post'), ['password' => 145678]);

    $response->assertSessionHasErrors(['password']);

    $response = $this->post(route('login.post'), ['password' => 'pass']);

    $response->assertSessionHasErrors(['password']);

    $response = $this->post(route('login.post'), ['email' => $user->email, 'password' => 'password']);

    $response->assertSessionHasNoErrors();
});
