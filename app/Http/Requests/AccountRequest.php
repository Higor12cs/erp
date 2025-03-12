<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'active' => 'required|boolean',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        $data['created_by'] = Auth::id();

        return $data;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser um texto',
            'name.max' => 'O nome não pode ter mais que 255 caracteres',
            'type.required' => 'O tipo é obrigatório',
            'type.string' => 'O tipo deve ser um texto',
            'type.max' => 'O tipo não pode ter mais que 255 caracteres',
            'bank_name.string' => 'O nome do banco deve ser um texto',
            'bank_name.max' => 'O nome do banco não pode ter mais que 255 caracteres',
            'agency.string' => 'A agência deve ser um texto',
            'agency.max' => 'A agência não pode ter mais que 255 caracteres',
            'account_number.string' => 'O número da conta deve ser um texto',
            'account_number.max' => 'O número da conta não pode ter mais que 255 caracteres',
            'active.required' => 'O status é obrigatório',
            'active.boolean' => 'O status deve ser um booleano',
        ];
    }
}
