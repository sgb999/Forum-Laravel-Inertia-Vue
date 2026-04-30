<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PostFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'user_id'  => ['sometimes', 'integer', 'exists:users,id'],
            'limit'    => ['sometimes', 'integer', 'min:1|max:100']
        ];
    }

    /**
     * Array of allowed filters
     *
     * @return array<string>
     */
    public function allowedFilters(): array
    {
        return [
            'category_id' => fn($query, $value) => $query->where('category_id', $value),
            'user_id'  => fn($query, $value) => $query->where('user_id', $value)
        ];
    }
}
