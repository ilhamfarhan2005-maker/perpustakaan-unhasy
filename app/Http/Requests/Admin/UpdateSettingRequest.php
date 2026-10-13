<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'digital_preview_pages' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'qris_image.image' => 'Berkas QRIS harus berupa gambar yang valid.',
            'qris_image.mimes' => 'Gambar QRIS harus berformat JPG, JPEG, atau PNG.',
            'qris_image.max' => 'Ukuran gambar QRIS maksimal 2 MB.',
        ];
    }
}
