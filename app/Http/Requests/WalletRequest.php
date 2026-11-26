<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('wallets', 'name')
                    ->where('user_id', $this->user()->id)
                    ->ignore($this->route('wallet')),
            ],
            'type'            => ['required', Rule::in(['cash', 'bank', 'ewallet', 'credit_card'])],
            'initial_balance' => ['sometimes', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'currency'        => ['sometimes', 'string', 'size:3'],
        ];
    }
}