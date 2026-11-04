<?php

use App\Models\User;

test('a guest sees the home link active and login links on desktop', function () {
    visit('/')
        ->assertAriaAttribute('.nav-home a', 'current', 'page')
        ->assertSee('Login')
        ->assertSee('Sign Up')
        ->assertDontSee('Profile')
        ->assertDontSee('Messages')
        ->assertDontSee('Sign out')
        ->assertMissing('.nav-toggle')
        ->assertPathIs('/')
        ->assertNoJavaScriptErrors();
});

test('a logged in user sees profile, messages and sign out links on desktop', function () {
    $user = User::factory()->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/')
        ->assertSee('Profile')
        ->assertSee('Messages')
        ->assertSee('Sign out')
        ->assertDontSee('Login')
        ->assertDontSee('Sign Up')
        ->assertNoJavaScriptErrors();
});

test('the active state moves to the categories link on the categories page', function () {
    visit('/categories')
        ->assertAriaAttribute('#main-navigation-panel .nav-links a', 'current', 'page')
        ->assertScript('!document.querySelector(".nav-home a").hasAttribute("aria-current")')
        ->assertPathIs('/categories')
        ->assertNoJavaScriptErrors();
});

test('the mobile menu opens with focus moved to the first link', function () {
    visit('/')
        ->resize(375, 812)
        ->click('.nav-toggle')
        ->assertAriaAttribute('.nav-toggle', 'expanded', 'true')
        ->assertVisible('.nav-backdrop')
        ->assertScript('document.activeElement === document.querySelector("#main-navigation-panel a")')
        ->assertNoJavaScriptErrors();
});

test('pressing escape closes the mobile menu and returns focus to the toggle', function () {
    visit('/')
        ->resize(375, 812)
        ->click('.nav-toggle')
        ->keys('#main-navigation-panel', 'Escape')
        ->assertAriaAttribute('.nav-toggle', 'expanded', 'false')
        ->assertScript('document.activeElement === document.querySelector(".nav-toggle")')
        ->assertNoJavaScriptErrors();
});

test('clicking a menu link navigates and closes the mobile menu', function () {
    visit('/')
        ->resize(375, 812)
        ->click('.nav-toggle')
        ->click('View Categories')
        ->assertPathIs('/categories')
        ->assertAriaAttribute('.nav-toggle', 'expanded', 'false')
        ->assertNoJavaScriptErrors();
});

test('signing out from the navigation returns a guest to the home page', function () {
    $user = User::factory()->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/')
        ->click('OK')
        ->assertSee('Sign out')
        ->click('Sign out')
        ->assertPathIs('/')
        ->assertSee('Login')
        ->assertSee('Sign Up')
        ->assertDontSee('Sign out')
        ->assertNoJavaScriptErrors();
});
