<?php

namespace App\Http\Requests;

use App\Models\Receivable;
use Illuminate\Foundation\Http\FormRequest;

class ReceivablePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'total_paid_amount' => $this->convertToNumber($this->total_paid_amount),
            'payments' => collect($this->payments)->map(function ($payment) {
                return [
                    'receivable_id' => $payment['receivable_id'],
                    'paid_amount' => $this->convertToNumber($payment['paid_amount']),
                    'fees' => $this->convertToNumber($payment['fees']),
                    'discount' => $this->convertToNumber($payment['discount']),
                ];
            })->toArray(),
        ]);
    }

    public function rules(): array
    {
        return [
            'receivable_ids' => 'required|array',
            'receivable_ids.*' => 'exists:receivables,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'account_id' => 'required|exists:accounts,id',
            'payment_date' => 'required|date',
            'total_paid_amount' => 'required|numeric|min:0.01',
            'payments' => 'required|array',
            'payments.*.receivable_id' => 'required|exists:receivables,id',
            'payments.*.paid_amount' => 'required|numeric|min:0',
            'payments.*.fees' => 'nullable|numeric|min:0',
            'payments.*.discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->payments as $index => $payment) {
                $receivable = Receivable::findOrFail($payment['receivable_id']);

                if (! $receivable) {
                    continue;
                }

                if (floatval($payment['paid_amount']) > floatval($receivable->remaining_amount)) {
                    $validator->errors()->add(
                        "payments.{$index}.paid_amount",
                        "O valor de pagamento não pode exceder o saldo restante de {$receivable->remaining_amount}"
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'receivable_ids.required' => 'É necessário selecionar pelo menos um recebível.',
            'payment_method_id.required' => 'O método de pagamento é obrigatório.',
            'account_id.required' => 'A conta é obrigatória.',
            'payment_date.required' => 'A data de pagamento é obrigatória.',
            'total_paid_amount.required' => 'O valor total pago é obrigatório.',
            'total_paid_amount.numeric' => 'O valor total pago deve ser um número.',
            'total_paid_amount.min' => 'O valor total pago deve ser maior que zero.',
            'payments.required' => 'É necessário informar os detalhes de pagamento.',
            'payments.*.paid_amount.required' => 'O valor pago é obrigatório.',
            'payments.*.paid_amount.numeric' => 'O valor pago deve ser um número.',
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
