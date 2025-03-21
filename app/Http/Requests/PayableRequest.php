<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payables = $this->payables;

        foreach ($payables as $key => $payable) {
            $payables[$key]['total_amount'] = $this->convertToNumber($payable['total_amount']);
        }

        $this->merge([
            'payables' => $payables,
        ]);
    }

    public function rules(): array
    {
        if ($this->routeIs('payables.update')) {
            return [
                'due_date' => 'required|date',
            ];
        }

        return [
            'payables' => 'required|array|min:1',
            'payables.*.supplier_id' => 'required|exists:suppliers,id',
            'payables.*.chart_account_id' => 'required|exists:chart_accounts,id',
            'payables.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payables.*.issue_date' => 'required|date',
            'payables.*.due_date' => 'required|date|after_or_equal:payables.*.issue_date',
            'payables.*.total_amount' => 'required|numeric|min:0.01',
            'payables.*.description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'payables.required' => 'É necessário informar pelo menos um pagável.',
            'payables.min' => 'É necessário informar pelo menos um pagável.',
            'payables.*.supplier_id.required' => 'O fornecedor é obrigatório.',
            'payables.*.supplier_id.exists' => 'O fornecedor informado é inválido.',
            'payables.*.chart_account_id.required' => 'A conta contábil é obrigatória.',
            'payables.*.chart_account_id.exists' => 'A conta contábil informada é inválida.',
            'payables.*.payment_method_id.required' => 'O método de pagamento é obrigatório.',
            'payables.*.payment_method_id.exists' => 'O método de pagamento informado é inválido.',
            'payables.*.issue_date.required' => 'A data de emissão é obrigatória.',
            'payables.*.due_date.required' => 'A data de vencimento é obrigatória.',
            'payables.*.due_date.after_or_equal' => 'A data de vencimento deve ser posterior ou igual à data de emissão.',
            'payables.*.total_amount.required' => 'O valor total é obrigatório.',
            'payables.*.total_amount.numeric' => 'O valor total deve ser um número.',
            'payables.*.total_amount.min' => 'O valor total deve ser maior que zero.',
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
