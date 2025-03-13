<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchasePayableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $payables = $this->payables;

        foreach ($payables as $key => $payable) {
            $payables[$key]['amount'] = $this->convertToNumber($payable['amount']);
        }

        $this->merge([
            'payables' => $payables,
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
            'payables' => 'required|array|min:1',
            'payables.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payables.*.due_date' => 'required|date',
            'payables.*.amount' => 'required|numeric|min:0.01',
            'payables.*.description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'payables.required' => 'É necessário incluir pelo menos um pagável',
            'payables.array' => 'O formato dos pagáveis é inválido',
            'payables.min' => 'É necessário incluir pelo menos um pagável',
            'payables.*.payment_method_id.required' => 'É necessário selecionar um método de pagamento',
            'payables.*.payment_method_id.exists' => 'O método de pagamento selecionado é inválido',
            'payables.*.due_date.required' => 'A data de vencimento é obrigatória',
            'payables.*.due_date.date' => 'A data de vencimento é inválida',
            'payables.*.amount.required' => 'O valor é obrigatório',
            'payables.*.amount.numeric' => 'O valor deve ser um número',
            'payables.*.amount.min' => 'O valor deve ser maior que zero',
            'payables.*.description.max' => 'A descrição não pode ter mais que 255 caracteres',
        ];
    }
}
