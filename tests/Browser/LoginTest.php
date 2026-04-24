<?php

use App\Models\User;

it('Can not login with incorrect password', function () {
   // $page = visit(route('log-out'));
    $page = visit(route('login.index'));

    $page->assertSee('Login');

    $user = User::factory()->create(['password' => bcrypt('password')]);

    $page->fill('email', $user->email);
    $page->fill('password', 'password1234');

    $page->assertEnabled('[data-testid="login-button"]');
    $page->pressAndWaitFor('[data-testid="login-button"]', 1);
    $page->assertSeeIn('.swal2-title', 'The provided credentials do not match our records.');
    $page->assertUrlIs(route('login.index'));
});

it('Can Login', function () {
    $page = visit(route('login.index'));

    $page->assertSee('Login');

    $user = User::factory()->create(['password' => bcrypt('password')]);

    $page->fill('email', $user->email);
    $page->fill('password', 'password');
    $page->assertEnabled('[data-testid="login-button"]');
    $page->pressAndWaitFor('[data-testid="login-button"]', 1);
    $page->assertSeeIn('.swal2-title', 'You are now logged in!');
    $page->assertUrlIs(route('home'));
});
