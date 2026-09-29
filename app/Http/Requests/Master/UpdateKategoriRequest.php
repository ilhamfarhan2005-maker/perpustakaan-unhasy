<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        $id = $this->route('kategori')?->id;

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($id)],
        ];
    }
}
