<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'seller_id' => 'nullable|exists:sellers,id',
            'issue_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'fees' => 'nullable|numeric|min:0',
            'observation' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.fees' => 'nullable|numeric|min:0',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'discount' => $this->convertToNumber($this->discount),
            'fees' => $this->convertToNumber($this->fees),
        ]);
    }

    private function convertToNumber($value)
    {
        if (is_null($value)) {
            return null;
        }

        $value = str_replace(',', '.', $value);

        return floatval(preg_replace('/[^\d.]/', '', $value));
    }
}
