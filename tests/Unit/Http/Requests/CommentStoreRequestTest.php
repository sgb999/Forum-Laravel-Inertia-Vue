<?php

use App\Http\Requests\Comments\CommentStoreRequest;
use Illuminate\Support\Facades\Validator;

function validateCommentPayload(array $payload): Illuminate\Contracts\Validation\Validator
{
    return Validator::make($payload, new CommentStoreRequest()->rules());
}

test('authorization is always granted', function () {
    expect(new CommentStoreRequest()->authorize())->toBeTrue();
});

test('a comment of at least four characters passes validation', function () {
    expect(validateCommentPayload([
        'comment' => 'Hello!',
    ])->errors()->all())->toBe([]);
});

test('the comment fails validation when it is missing, not a string, or too short', function (array $payload) {
    expect(validateCommentPayload($payload)->errors()->keys())->toBe(['comment']);
})->with([
    'missing comment' => [[]],
    'comment is not a string' => [['comment' => 12345]],
    'comment is shorter than four characters' => [['comment' => 'abc']],
]);
