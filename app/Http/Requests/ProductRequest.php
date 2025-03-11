<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cost' => $this->convertToNumber($this->cost),
            'price' => $this->convertToNumber($this->price),
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

    public function rules(): array
    {
        return [
            'brand_id' => 'nullable|exists:brands,id',
            'group_id' => 'nullable|exists:groups,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:255',
            'cost' => 'required|numeric|gt:0',
            'price' => 'required|numeric|gt:0',
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
            'brand_id.exists' => 'A marca informada não existe',
            'group_id.exists' => 'O grupo informado não existe',
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser um texto',
            'name.max' => 'O nome não pode ter mais que 255 caracteres',
            'description.string' => 'A descrição deve ser um texto',
            'sku.string' => 'O SKU deve ser um texto',
            'sku.max' => 'O SKU não pode ter mais que 255 caracteres',
            'cost.required' => 'O custo é obrigatório',
            'cost.numeric' => 'O custo deve ser um número',
            'cost.gt' => 'O custo deve ser maior que zero',
            'price.required' => 'O preço é obrigatório',
            'price.numeric' => 'O preço deve ser um número',
            'price.gt' => 'O preço deve ser maior que zero',
        ];
    }
}
