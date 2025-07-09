<?php

namespace App\Http\Requests\Trabajador;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificamos si el usuario está autenticado
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'patente' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'capacidad' => 'required|integer',
        ];
    }
}
