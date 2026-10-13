<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyFinePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['setujui', 'tolak'])],
            'rejection_note' => ['required_if:action,tolak', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_note.required_if' => 'Alasan penolakan wajib diisi.',
        ];
    }
}
