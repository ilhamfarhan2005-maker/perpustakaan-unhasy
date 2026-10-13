<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        return [
            'nim_nip' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return ['nim_nip' => 'NIM/NIP anggota', 'isbn' => 'ISBN/kode inventaris buku'];
    }
}
