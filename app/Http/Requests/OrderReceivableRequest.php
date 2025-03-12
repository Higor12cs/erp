<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $receivables = $this->receivables;

        foreach ($receivables as $key => $receivable) {
            $receivables[$key]['amount'] = $this->convertToNumber($receivable['amount']);
        }

        $this->merge([
            'receivables' => $receivables,
        ]);
    }

    private function convertToNumber($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return floatval($value);
        }

        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return floatval(preg_replace('/[^\d.]/', '', $value));
    }

    public function rules(): array
    {
        return [
            'receivables' => 'required|array|min:1',
            'receivables.*.payment_method_id' => 'required|exists:payment_methods,id',
            'receivables.*.due_date' => 'required|date',
            'receivables.*.amount' => 'required|numeric|min:0.01',
            'receivables.*.description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'receivables.required' => 'É necessário incluir pelo menos um recebível',
            'receivables.array' => 'O formato dos recebíveis é inválido',
            'receivables.min' => 'É necessário incluir pelo menos um recebível',
            'receivables.*.payment_method_id.required' => 'É necessário selecionar um método de pagamento',
            'receivables.*.payment_method_id.exists' => 'O método de pagamento selecionado é inválido',
            'receivables.*.due_date.required' => 'A data de vencimento é obrigatória',
            'receivables.*.due_date.date' => 'A data de vencimento é inválida',
            'receivables.*.amount.required' => 'O valor é obrigatório',
            'receivables.*.amount.numeric' => 'O valor deve ser um número',
            'receivables.*.amount.min' => 'O valor deve ser maior que zero',
            'receivables.*.description.max' => 'A descrição não pode ter mais que 255 caracteres',
        ];
    }
}
