<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:30', 'unique:books,isbn'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'rak_location' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori tidak valid.',
            'isbn.unique' => 'ISBN ini sudah terdaftar pada buku lain.',
            'cover_image.image' => 'File cover harus berupa gambar.',
            'cover_image.max' => 'Ukuran cover maksimal 2MB.',
        ];
    }
}
