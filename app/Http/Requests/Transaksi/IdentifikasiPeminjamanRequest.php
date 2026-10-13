<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

class IdentifikasiPeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        return [
            'nim_nip' => ['nullable', 'string', 'max:255', 'required_without:isbn'],
            'isbn' => ['nullable', 'string', 'max:255', 'required_without:nim_nip'],
        ];
    }
}
