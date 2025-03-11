<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_id' => 'required|exists:sections,id',
            'name' => 'required|string|max:255',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['created_by'] = Auth::id();

        return $data;
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'A seção é obrigatória',
            'section_id.exists' => 'A seção informada não existe',
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser um texto',
            'name.max' => 'O nome não pode ter mais que 255 caracteres',
        ];
    }
}
