<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "client_id"         => "required|exists:clients,id",
            "service_type"      => "required|exists:service_types,slug",
            "scheduled_date"    => "required|date",
            "address"           => "required|string|max:255",
            "priority"          => "required|in:alta,media,baja",
            "description"       => "nullable|string",
            "assigned_to"       => "nullable|exists:users,id"
        ];
    }

    public function messages(): array
    {
        return [
            "client_id.required"        => "El campo cliente es obligatorio.",
            "service_type.required"  => "El campo tipo de servicio es obligatorio.",
            "scheduled_date.required"   => "El campo fecha programada es obligatorio.",
            "address.required"          => "El campo dirección es obligatorio.",
            "priority.required"         => "El campo prioridad es obligatorio.",
            "status.required"           => "El campo estado es obligatorio.",
            "description.required"      => "El campo descripción es obligatorio.",
            "assigned_to.required"      => "El campo asignado a es obligatorio.",
        ];
    }
}
