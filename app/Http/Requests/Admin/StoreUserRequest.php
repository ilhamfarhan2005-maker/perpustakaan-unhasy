<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nim_nip' => ['nullable', 'string', 'max:30', 'unique:users,nim_nip'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,pustakawan,mahasiswa,kepala_perpustakaan'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
