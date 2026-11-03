<?php

use App\Models\User;

test('the register page renders for guests', function () {
    visit('/register')
        ->assertSee('Register an account')
        ->assertSee('Name')
        ->assertSee('Username')
        ->assertSee('E-mail')
        ->assertSee('Confirm Password')
        ->assertPathIs('/register')
        ->assertNoJavaScriptErrors();
});

test('a guest can register an account', function () {
    visit('/register')
        ->fill('name', 'Ada Lovelace')
        ->fill('username', 'ada')
        ->fill('email', 'ada@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->click('Register')
        ->assertPathIs('/')
        ->assertNoJavaScriptErrors();

    $this->assertDatabaseHas(User::class, [
        'name' => 'Ada Lovelace',
        'username' => 'ada',
        'email' => 'ada@example.com',
    ]);
});

test('registration shows an error when the password confirmation does not match', function () {
    visit('/register')
        ->fill('name', 'Ada Lovelace')
        ->fill('username', 'ada')
        ->fill('email', 'ada@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'different-password')
        ->click('Register')
        ->assertPathIs('/register')
        ->assertSee('The password confirmation does not match.')
        ->assertNoJavaScriptErrors();

    $this->assertDatabaseCount(User::class, 0);
});
