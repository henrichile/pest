<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client');
        $clientId = is_object($clientId) ? $clientId->id : $clientId;
        
        return [
            'name' => 'required|string|max:255',
            'rut' => 'required|string|max:20|unique:clients,rut,' . $clientId,
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'business_type' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
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



