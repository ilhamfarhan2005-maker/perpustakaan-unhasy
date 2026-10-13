<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMahasiswa() === true;
    }

    public function rules(): array
    {
        return [
            'fines' => ['required', 'array', 'min:1', 'max:50'],
            'fines.*' => ['required', 'integer', 'distinct', 'exists:fines,id'],
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'fines.required' => 'Pilih sekurang-kurangnya satu denda yang akan dibayar.',
            'fines.min' => 'Pilih sekurang-kurangnya satu denda yang akan dibayar.',
            'proof.required' => 'Bukti pembayaran wajib diunggah.',
            'proof.image' => 'Bukti pembayaran harus berupa gambar yang valid.',
            'proof.mimes' => 'Bukti pembayaran harus berformat JPG, JPEG, atau PNG.',
            'proof.max' => 'Ukuran bukti pembayaran maksimal 2 MB.',
        ];
    }
}
