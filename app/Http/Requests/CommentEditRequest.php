<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CommentEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('comment')->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules() : array
    {
        return [
            'comment' => ['required', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->input('comment') === $this->route('comment')->comment) {
                    abort(422);
                }
            }
        ];
    }
}
