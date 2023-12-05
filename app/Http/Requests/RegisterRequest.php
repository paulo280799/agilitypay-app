<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255|min:1',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|max:255|min:8',
            'cpf' => 'required|string|max:11|min:11|unique:users,cpf',
        ];
    }
}
