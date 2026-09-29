<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('pengguna')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'nim_nip' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nim_nip')->ignore($id)],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,pustakawan,mahasiswa,kepala_perpustakaan'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }
}
