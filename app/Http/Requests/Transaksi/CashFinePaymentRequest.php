<?php

namespace App\Http\Requests\Transaksi;

use App\Models\Fine;
use Illuminate\Foundation\Http\FormRequest;

class CashFinePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'pustakawan'], true);
    }

    public function rules(): array
    {
        $fine = $this->route('fine');

        return [
            'paid_amount' => [
                'required',
                'numeric',
                'min:'.($fine instanceof Fine ? $fine->amount : 0),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'paid_amount.min' => 'Jumlah pembayaran kurang dari total denda.',
        ];
    }
}
