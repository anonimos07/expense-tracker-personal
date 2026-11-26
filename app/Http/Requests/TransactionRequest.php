<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')->where('user_id', $userId),
            ],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('user_id', $userId),
            ],
            'type'        => ['required', Rule::in(['expense', 'income'])],
            'amount'      => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'notes'       => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty() || ! $this->filled('category_id')) {
                    return;
                }

                $category = $this->user()->categories()->find($this->input('category_id'));

                if ($category && $category->type !== $this->input('type')) {
                    $validator->errors()->add(
                        'category_id',
                        'The category type must match the transaction type.'
                    );
                }
            },
        ];
    }
}