<?php

namespace App\Http\Controllers;

use App\Models\Embarcacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Actualizar la ubicación de la embarcación.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Embarcacion $embarcacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Embarcacion $embarcacion)
    {
        // Validación de las coordenadas (latitud y longitud)
        $request->validate([
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        try {
            // Actualizar las coordenadas de la embarcación
            $embarcacion->update([
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
            ]);

            return response()->json(['message' => 'Ubicación actualizada correctamente.']);
        } catch (\Exception $e) {
            // Registrar el error si no se puede actualizar
            Log::error('Error al actualizar la ubicación: ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un problema al actualizar la ubicación.'], 500);
        }
    }
}
