<?php

use App\Models\User;

test('the login page renders for guests', function () {
    visit('/login')
        ->assertSee('Login')
        ->assertSee('E-mail')
        ->assertSee('Password')
        ->assertPathIs('/login')
        ->assertNoJavaScriptErrors();
});

test('the login button is disabled until both fields are filled', function () {
    visit('/login')
        ->assertButtonDisabled('@login-button')
        ->fill('email', 'ada@example.com')
        ->assertButtonDisabled('@login-button')
        ->fill('password', 'password')
        ->assertButtonEnabled('@login-button');
});

test('a user can log in and is redirected to the posts page', function () {
    $user = User::factory()->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/')
        ->assertNoJavaScriptErrors()
        ->assertSee('Sign out')
        ->assertDontSee('Sign Up');
});

test('login shows an error when the credentials do not match a user', function () {
    $user = User::factory()->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'wrong-password')
        ->click('@login-button')
        ->assertPathIs('/login')
        ->assertSee('The provided credentials do not match our records.')
        ->assertSee('Sign Up')
        ->assertNoJavaScriptErrors();
});
