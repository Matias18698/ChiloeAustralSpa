<?php

namespace App\Http\Controllers;

use App\Models\Embarcacion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Models\Trabajador;
use Illuminate\Support\Facades\Http;

class EmbarcacionController extends Controller
{
    /**
     * Lista de embarcaciones.
     */
    public function index()
    {
        //de cada enbarcación usar el imei y token para obtener la ubicación
        $embarcaciones= Embarcacion::all();
        foreach ($embarcaciones as $embarcacion) {
            try {
                $gpsData = $this->gps($embarcacion->imei);
                $embarcacion->lat = $gpsData['latitud'];
                $embarcacion->lon = $gpsData['longitud'];
            } catch (\Exception $e) {
                Log::error('Error al obtener GPS para embarcación ' . $embarcacion->id . ': ' . $e->getMessage());
                // Puedes manejar el error como desees, por ejemplo, asignar valores nulos
                $embarcacion->lat = 0;
                $embarcacion->lon = 0;
            }
        }
        return Inertia::render('Embarcacion/Index', [
            'embarcaciones' => $embarcaciones
        ]);
    }


    function gps($imei)
    {
        $Token = 'a92514bc-3205-47f4-9890-ba7b4fc85bd7';
        $response = Http::asForm()->post('https://login.tecsat.cl/WS/WStrack2.asmx/GetCurrentPositionByIMEI', [
            'SecurityToken' => $Token,
            'IMEI' => $imei,
        ]);

        if ($response->successful()) {
            // Retorna XML: <string xmlns="http://www.tempuri.org/">...</string>
            $xml = (string) $response;
            $latitud = $this->xmlToData($xml, 'Lat');
            $longitud = $this->xmlToData($xml, 'Lon');
            return [
                'latitud' => $latitud,
                'longitud' => $longitud
            ];
        }

        throw new \Exception('Error en la API: ' . $response->status());
    }

    function xmlToData($xml,$tag){
        $length = strlen($tag);
        $i = stripos($xml, $tag); //donde comienza el tag 
        $f = stripos($xml, "/".$tag); //donde termina el tag
        //$i + $length + 4 donde empieza la cadena + el largo del tag + 4 de los caracteres especiales </>
        //$f - $i - $length - 8 donde termina la cadena - donde empieza la cadena - el largo del tag - 8 de los caracteres especiales </>
        return substr($xml, $i + $length + 4, $f - $i - $length - 8);
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return Inertia::render('Embarcacion/Create');
    }

    /**
     * Guardar embarcación.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'patente' => 'required|string|max:255|unique:embarcaciones',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|string|in:activo,inactivo',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'imei' => 'required|string|max:255',
        ]);

        try {
            Embarcacion::create($data);
            return redirect()->route('embarcacion.index')->with('success', 'Embarcación creada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear embarcación: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Hubo un problema al crear la embarcación.']);
        }
    }

    /**
     * Editar embarcación.
     */
    public function edit(Embarcacion $embarcacion)
    {
        return Inertia::render('Embarcacion/Edit', [
            'embarcacion' => $embarcacion
        ]);
    }

    /**
     * Actualizar embarcación.
     */
    public function update(Request $request, Embarcacion $embarcacion)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'patente' => 'required|string|max:255|unique:embarcaciones,patente,' . $embarcacion->id,
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|string|in:activo,inactivo',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        $embarcacion->update($data);

        return redirect()->route('embarcacion.index')->with('success', 'Embarcación actualizada correctamente.');
    }

    /**
     * Eliminar embarcación.
     */
    public function destroy(Embarcacion $embarcacion)
    {
        try {
            $embarcacion->delete();
            return redirect()->route('embarcacion.index')->with('success', 'Embarcación eliminada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar embarcación: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Hubo un problema al eliminar la embarcación.']);
        }
    }

    /**
     * Mostrar detalles.
     */
    public function show(Embarcacion $embarcacion)
    {
        return Inertia::render('Embarcacion/Show', [
            'embarcacion' => $embarcacion
        ]);
    }

    /**
     * Recibir actualización de ubicación desde la tablet.
     */
// app/Http/Controllers/EmbarcacionController.php

public function updateLocation(Request $request, Embarcacion $embarcacion)
{
    // Validar las coordenadas (latitud y longitud)
    $request->validate([
        'latitud' => 'required|numeric',
        'longitud' => 'required|numeric',
    ]);

    try {
        // Actualizar las coordenadas en la base de datos
        $embarcacion->update([
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
        ]);

        return response()->json(['message' => 'Ubicación actualizada correctamente.']);
    } catch (\Exception $e) {
        // Registrar el error si ocurre un problema
        Log::error('Error al actualizar la ubicación: ' . $e->getMessage());
        return response()->json(['error' => 'Hubo un problema al actualizar la ubicación.'], 500);
    }
}

}
