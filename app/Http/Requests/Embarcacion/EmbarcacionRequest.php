<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EmbarcacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verificamos si el usuario está autenticado
        return Auth::check();
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'id' => 'nullable|integer',
            'tipo' => 'required|string|max:255',
            'patente' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'capacidad' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre de la embarcación es obligatorio.',
            'tipo.required' => 'El tipo de la embarcación es obligatorio.',
            'patente.required' => 'La patente de la embarcación es obligatoria.',
            'estado.required' => 'El estado de la embarcación es obligatorio.',
            'capacidad.required' => 'La capacidad de la embarcación es obligatoria.',
        ];
    }
}
