<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $isUpdate = ! $this->isMethod('post');

        $type = $isUpdate
            ? $user->categories()->whereKey($this->route('category'))->value('type')
            : $this->input('type');

        $rules = [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'name')
                    ->where('user_id', $user->id)
                    ->where('type', $type)
                    ->ignore($this->route('category')),
            ],
            'color' => ['sometimes', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon'  => ['nullable', 'string', 'max:50'],
        ];

        if (! $isUpdate) {
            $rules['type'] = ['required', Rule::in(['expense', 'income'])];
        }

        return $rules;
    }
}