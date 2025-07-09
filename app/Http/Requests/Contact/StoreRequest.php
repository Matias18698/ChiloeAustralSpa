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
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'avatar' => 'nullable|image|max:2048',
            'rut' => 'required|string|max:20|unique:trabajadores,rut', // Sin necesidad de exclusión en creación
            'fecha_nacimiento' => 'required|date',
            'estado_civil' => 'required|string|max:50',
            'nacionalidad' => 'required|string|max:50',
            'direccion' => 'required|string|max:255',
            'comuna' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:trabajadores,email', // Sin necesidad de exclusión en creación
            'telefono' => 'required|string|max:15|unique:trabajadores,telefono', // Sin necesidad de exclusión en creación
            'afp' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'tamaño_ropa' => 'required|string|max:50',
            'tipo_contrato' => 'required|string|max:50',
            'sueldo_real' => 'required|numeric|min:0|max:9999999.99',
            'sueldo_liquidacion' => 'required|numeric|min:0|max:9999999.99',
            
            'user_id' => 'required|exists:users,id'
        ];
    }
}
