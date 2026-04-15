<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->route('id'),
            'password' => $this->isMethod('post') ? 'required|string|min:8' : 'nullable|string|min:8',
            'phone' => 'required|string|max:20',
            'photo' => 'required|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ];
    }
}
