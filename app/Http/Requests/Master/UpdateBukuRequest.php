<?php

namespace App\Http\Requests\Master;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBukuRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $book = $this->route('buku');
        $inventoryCode = $this->input('kode_inventaris');

        $this->merge([
            'format' => $this->input('format', $book instanceof Book ? $book->format : 'fisik'),
            'kode_inventaris' => is_string($inventoryCode) ? trim($inventoryCode) : $inventoryCode,
        ]);
    }

    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        $id = $this->route('buku')?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:30', Rule::unique('books', 'isbn')->ignore($id)],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'format' => ['required', Rule::in(Book::FORMATS)],
            'rak_location' => ['required_if:format,fisik,fisik_digital', 'nullable', 'string', 'max:50'],
            'stock' => ['required_if:format,fisik,fisik_digital', 'nullable', 'integer', 'min:0'],
            'digital_url' => ['nullable', 'url:https'],
            'preview_url' => [
                'nullable',
                'url:https',
                'regex:/^https:\\/\\//i',
            ],
            'digital_file' => ['nullable', 'file', 'mimetypes:application/pdf,application/x-pdf', 'max:20480'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori tidak valid.',
            'isbn.unique' => 'ISBN ini sudah terdaftar pada buku lain.',
            'format.in' => 'Format buku tidak valid.',
            'rak_location.required_if' => 'Rak wajib diisi untuk buku yang memiliki bentuk fisik.',
            'stock.required_if' => 'Stok wajib diisi untuk buku yang memiliki bentuk fisik.',
            'digital_file.mimetypes' => 'File buku harus berupa PDF yang valid.',
            'digital_file.max' => 'Ukuran file buku maksimal 20 MB.',
            'preview_url.url' => 'URL pratinjau harus berupa tautan HTTPS yang valid.',
            'preview_url.regex' => 'URL pratinjau harus diawali dengan https://.',
            'digital_url.url' => 'URL buku digital harus berupa tautan HTTPS yang valid.',
            'cover_image.image' => 'File cover harus berupa gambar.',
            'cover_image.max' => 'Ukuran cover maksimal 2MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $book = $this->route('buku');
            if (in_array($this->input('format'), ['digital', 'fisik_digital'], true) && ! $this->hasFile('digital_file') && blank($this->input('preview_url')) && ! $book?->file_path) $validator->errors()->add('digital_file', 'Buku digital wajib memiliki file PDF atau URL pratinjau.');
            if ($this->hasFile('digital_file') && file_get_contents($this->file('digital_file')->getRealPath(), false, null, 0, 4) !== '%PDF') $validator->errors()->add('digital_file', 'Isi file buku bukan PDF yang valid.');
        });
    }
}
