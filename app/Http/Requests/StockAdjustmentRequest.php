<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'new_quantity' => $this->convertToNumber($this->new_quantity),
        ]);
    }

    public function rules(): array
    {
        return [
            'new_quantity' => 'required|numeric|min:0',
            'notes' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'new_quantity.required' => 'A quantidade é obrigatória',
            'new_quantity.numeric' => 'A quantidade deve ser um número',
            'new_quantity.min' => 'A quantidade deve ser maior ou igual a zero',
            'notes.required' => 'A descrição do ajuste é obrigatória',
            'notes.string' => 'A descrição deve ser um texto',
            'notes.max' => 'A descrição não pode ter mais que 255 caracteres',
        ];
    }

    private function convertToNumber($value): ?float
    {
        if (is_null($value)) {
            return null;
        }

        $value = str_replace(',', '.', $value);

        return floatval(preg_replace('/[^\d.]/', '', $value));
    }
}
