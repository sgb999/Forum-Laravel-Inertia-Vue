<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules() : array
    {
        return [
            'name'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'username' => ['sometimes', 'nullable', 'max:255', 'string', 'unique:users,username'],
            'email'    => ['sometimes', 'nullable', 'email', 'unique:users,email', 'max:255'],
            'password' => ['sometimes', 'nullable', 'min:8', 'max:255', 'confirmed'],
            'avatar' => ['sometimes', 'nullable', 'image', 'max:1024'],
            'banner' => ['sometimes', 'nullable', 'image', 'max:2048']
        ];
    }
}
