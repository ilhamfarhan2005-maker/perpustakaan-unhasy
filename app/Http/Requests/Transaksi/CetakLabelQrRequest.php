<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

class CetakLabelQrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        return [
            'books' => ['required', 'array', 'min:1', 'max:100'],
            'books.*' => ['required', 'integer', 'distinct', 'exists:books,id'],
        ];
    }

    public function attributes(): array
    {
        return ['books' => 'buku yang dipilih'];
    }
}
