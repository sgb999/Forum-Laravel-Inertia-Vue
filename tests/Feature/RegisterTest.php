<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

function registerPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ada Lovelace',
        'username' => 'ada',
        'email' => 'ada@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $overrides);
}

test('the register page renders for guests', function () {
    $this->get(route('register.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('user/Register'));
});

test('valid registration creates the user with a hashed password, signs them in, and redirects to posts', function () {
    $response = $this->post(route('register.post'), registerPayload());

    $response->assertRedirectToRoute('post.show');

    $this->assertDatabaseHas(User::class, [
        'name' => 'Ada Lovelace',
        'username' => 'ada',
        'email' => 'ada@example.com',
    ]);
    $this->assertDatabaseCount(User::class, 1);
    $this->assertCredentials([
        'email' => 'ada@example.com',
        'password' => 'password',
    ]);

    $user = User::query()->where('email', 'ada@example.com')->sole();

    $this->assertModelExists($user);
    $this->assertAuthenticatedAs($user);
    expect($user->password)->not->toBe('password')
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

test('registration requires name, username, email, and password', function () {
    $response = $this->post(route('register.post'));

    $response->assertSessionHasErrors(['name', 'username', 'email', 'password']);
    $this->assertGuest();
    $this->assertDatabaseCount(User::class, 0);
});

test('registration rejects an invalid email address', function () {
    $response = $this->post(route('register.post'), registerPayload(['email' => 'not-an-email']));

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
    $this->assertDatabaseCount(User::class, 0);
});

test('registration rejects an email or username that already belongs to a user', function (string $field) {
    $existing = User::factory()->create();

    $response = $this->post(route('register.post'), registerPayload([
        $field => $existing->{$field},
    ]));

    $response->assertSessionHasErrors([$field]);
    $this->assertDatabaseCount(User::class, 1);
})->with(['email', 'username']);

test('registration rejects a password shorter than eight characters', function () {
    $response = $this->post(route('register.post'), registerPayload([
        'password' => '1234567',
        'password_confirmation' => '1234567',
    ]));

    $response->assertSessionHasErrors(['password']);
    $this->assertGuest();
    $this->assertDatabaseCount(User::class, 0);
});

test('registration rejects a password that does not match its confirmation', function () {
    $response = $this->post(route('register.post'), registerPayload([
        'password_confirmation' => 'different-password',
    ]));

    $response->assertSessionHasErrors(['password']);
    $this->assertGuest();
    $this->assertDatabaseCount(User::class, 0);
});

test('registration rejects a name or username longer than 255 characters', function (string $field) {
    $response = $this->post(route('register.post'), registerPayload([
        $field => str_repeat('a', 256),
    ]));

    $response->assertSessionHasErrors([$field]);
    $this->assertDatabaseCount(User::class, 0);
})->with(['name', 'username']);

test('registration attaches a valid avatar to the avatar media collection', function () {
    Storage::fake('public');

    $this->post(route('register.post'), registerPayload([
        'avatar' => UploadedFile::fake()->image('avatar.jpg'),
    ]))->assertRedirectToRoute('post.show');

    $user = User::query()->where('email', 'ada@example.com')->sole();

    expect($user->getMedia('avatar'))->toHaveCount(1);
});

test('registration rejects an avatar that is not an image', function () {
    $response = $this->post(route('register.post'), registerPayload([
        'avatar' => UploadedFile::fake()->create('avatar.txt', 100, 'text/plain'),
    ]));

    $response->assertSessionHasErrors(['avatar']);
    $this->assertGuest();
    $this->assertDatabaseCount(User::class, 0);
});

test('authenticated users are redirected away from the register page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('register.index'))
        ->assertRedirectToRoute('post.show');
});
