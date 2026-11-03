<?php

use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Validator;

function validateLoginPayload(array $payload): Illuminate\Contracts\Validation\Validator
{
    return Validator::make($payload, new UserLoginRequest()->rules());
}

test('authorization is always granted', function () {
    expect(new UserLoginRequest()->authorize())->toBeTrue();
});

test('a valid email and a password of at least eight characters pass validation', function () {
    expect(validateLoginPayload([
        'email' => 'ada@example.com',
        'password' => 'password',
    ])->errors()->all())->toBe([]);
});

test('login credentials fail validation when a field is invalid', function (array $payload, string $invalidField) {
    expect(validateLoginPayload($payload)->errors()->keys())->toBe([$invalidField]);
})->with([
    'missing email' => [['password' => 'password'], 'email'],
    'email is not a string' => [['email' => 1546, 'password' => 'password'], 'email'],
    'email is not an address' => [['email' => 'not-an-email', 'password' => 'password'], 'email'],
    'missing password' => [['email' => 'ada@example.com'], 'password'],
    'password is not a string' => [['email' => 'ada@example.com', 'password' => 145678], 'password'],
    'password is shorter than eight characters' => [['email' => 'ada@example.com', 'password' => '1234567'], 'password'],
]);
