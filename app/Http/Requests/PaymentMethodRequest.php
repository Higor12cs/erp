<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PaymentMethodRequest extends FormRequest
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
            'active' => 'required|boolean',
        ];
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
            'active.required' => 'O status é obrigatório',
            'active.boolean' => 'O status deve ser um booleano',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        $data['created_by'] = Auth::id();

        return $data;
    }
}
