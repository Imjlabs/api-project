<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'first_name' => 'string|max:255',
            'email' => 'email',
            'phone_number' => 'string|max:20',
            'address' => 'string|max:255',
            'city' => 'string|max:255',
            'postal_code' => 'string|max:20',
            'siret_number' => 'nullable|string|max:20',
            'available_space' => 'nullable|numeric',
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->symbols(),
            ],
        ];
    }
}
