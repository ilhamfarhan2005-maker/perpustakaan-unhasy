<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProsesPengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['setujui', 'tolak', 'konfirmasi_kembali'])],
            'alasan_penolakan' => ['nullable', 'string', 'max:1000', 'required_if:action,tolak'],
        ];
    }
}
