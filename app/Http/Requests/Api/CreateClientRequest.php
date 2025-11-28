<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'rut' => 'required|string|max:20|unique:clients,rut',
            'business_type' => 'nullable|string|max:255',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:20',
            'contacts.*.position' => 'nullable|string|max:255',
            'contacts.*.is_primary' => 'nullable|boolean',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => 'El tipo debe ser natural o juridica.',
            'rut.unique' => 'El RUT ya está registrado.',
        ];
    }
}



