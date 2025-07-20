<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Trabajador;
use App\Models\Embarcacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BitacoraController extends Controller
{

    /**
     * Muestra la lista de bitácoras paginadas.
     */
    public function index()
    {
        $bitacoras = Bitacora::with(['trabajador', 'embarcacion'])
            ->orderByDesc('fecha')
            ->paginate(15);

        return Inertia::render('Bitacora/Index', [
            'bitacoras' => $bitacoras
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva bitácora.
     */
    public function create()
    {
        $trabajadores = Trabajador::all();
        $embarcaciones = Embarcacion::all();

        return Inertia::render('Bitacora/Create', [
            'trabajadores' => $trabajadores,
            'embarcaciones' => $embarcaciones,
        ]);
    }

    /**
     * Valida los datos para la creación o actualización de una bitácora.
     */
    private function validateBitacora(Request $request)
    {
        return $request->validate([
            'fecha' => 'required|date',
            'zona' => 'nullable|string',
            'centro' => 'nullable|string',
            'numero_boleta' => 'nullable|string',
            'embarcacion_id' => 'required|exists:embarcaciones,id',
            'trabajador_id' => 'required|exists:trabajadores,id',
            'hora_inicial' => 'nullable|date_format:H:i',
            'hora_final' => 'nullable|date_format:H:i|after:hora_inicial',
            'actividades_am' => 'nullable|string',
            'actividades_pm' => 'nullable|string',
            'firma_encargado' => 'nullable|string', // Ruta a la imagen de la firma
            'observaciones' => 'nullable|string',
            'total_jaulas' => 'nullable|integer|min:0',
            'dimension_jaulas' => 'nullable|string',
            // Validación para los buzos
            'buzo_1_id' => 'nullable|exists:trabajadores,id',
            'buzo_2_id' => 'nullable|exists:trabajadores,id',   
            'buzo_3_id' => 'nullable|exists:trabajadores,id',
            'buzo_4_id' => 'nullable|exists:trabajadores,id',
            'buzo_5_id' => 'nullable|exists:trabajadores,id',
        ]);
    }

    /**
     * Almacena una nueva bitácora.
        */
    public function store(Request $request)
    {
        $validated = $this->validateBitacora($request);

        // Procesar la firma si se envió
        if ($request->filled('firma_encargado')) {
            $image = $request->firma_encargado;

            // Asegurarse que sea base64
            if (Str::startsWith($image, 'data:image')) {
                // Limpiar y decodificar
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);

                // Generar nombre único y guardar
                $imageName = 'firmas/' . Str::uuid() . '.png';
                Storage::disk('public')->put($imageName, $imageData);

                // Guardar la ruta en los datos validados
                $validated['firma_encargado'] = $imageName;
            }
        }

        // Crear la bitácora con la ruta de la firma (si existe)
        Bitacora::create($validated);

        return redirect()->route('bitacora.index')->with('success', 'Bitácora registrada correctamente.');
    }

    /**
     * Muestra los detalles de una bitácora.
     */
    public function show(Bitacora $bitacora)
    {
        $bitacora->load(['trabajador', 'embarcacion', 'buzo1', 'buzo2', 'buzo3', 'buzo4', 'buzo5']);

        return Inertia::render('Bitacora/Show', [
            'bitacora' => $bitacora,
        ]);
    }

    /**
     * Muestra el formulario para editar una bitácora.
     */
    public function edit(Bitacora $bitacora)
    {
        $trabajadores = Trabajador::all();
        $embarcaciones = Embarcacion::all();

        return Inertia::render('Bitacora/Edit', [
            'bitacora' => $bitacora,
            'trabajadores' => $trabajadores,
            'embarcaciones' => $embarcaciones,
            'buzos' => [
                'buzo_1_id' => $bitacora->buzo1,
                'buzo_2_id' => $bitacora->buzo2,
                'buzo_3_id' => $bitacora->buzo3,
                'buzo_4_id' => $bitacora->buzo4,
                'buzo_5_id' => $bitacora->buzo5,
            ],
        ]);
    }

    /**
     * Actualiza una bitácora existente.
     */
public function update(Request $request, Bitacora $bitacora)
{
    $validated = $this->validateBitacoraEdit($request);

    // Procesar la firma si se envió
    if ($request->filled('firma_encargado') && Str::startsWith($request->firma_encargado, 'data:image')) {
        $image = str_replace('data:image/png;base64,', '', $request->firma_encargado);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);
        
    // Generar nombre único y guardar
        $imageName = 'firmas/' . Str::uuid() . '.png';
        Storage::disk('public')->put($imageName, $imageData);

        $validated['firma_encargado'] = $imageName;
    }

    $bitacora->update($validated);

    return redirect()->route('bitacora.index')->with('success', 'Bitácora actualizada correctamente.');
}


    /**
     * Elimina una bitácora.
     */
    public function destroy(Bitacora $bitacora)
    {
        $bitacora->delete();

        return redirect()->route('bitacora.index')->with('success', 'Bitácora eliminada correctamente.');
    }

    /**
     * Crea una bitácora diaria si no existe para la embarcación.
     */
    public function crearBitacoraDiaria($embarcacionId)
    {
        $hoy = now()->toDateString();

        $bitacora = Bitacora::firstOrCreate(
            ['embarcacion_id' => $embarcacionId, 'fecha' => $hoy],
            ['contenido' => 'Bitácora del día ' . $hoy]
        );

        return redirect()->route('bitacora.show', $bitacora);
    }

    /**
     * Muestra las bitácoras de una embarcación específica.
     */
    public function bitacorasPorEmbarcacion($embarcacionId)
    {
        $embarcacion = Embarcacion::with(['bitacoras' => function ($query) {
            $query->orderByDesc('fecha');
        }])->findOrFail($embarcacionId);

        return Inertia::render('Bitacora/PorEmbarcacion', [
            'embarcacion' => $embarcacion,
        ]);
    }

public function documento($id)
{
    $bitacora = Bitacora::with([
        'embarcacion',
        'trabajador',
        'buzo1',
        'buzo2',
        'buzo3',
        'buzo4',
        'buzo5',
    ])->findOrFail($id);

    return Inertia::render('Bitacora/Documento', [
        'bitacora' => $bitacora,
    ]);
}

    private function validateBitacoraEdit(Request $request)
    {
        return $request->validate([
            'fecha' => 'required|date',
            'zona' => 'nullable|string',
            'centro' => 'nullable|string',
            'numero_boleta' => 'nullable|string',
            'embarcacion_id' => 'required|exists:embarcaciones,id',
            'trabajador_id' => 'required|exists:trabajadores,id',
            'actividades_am' => 'nullable|string',
            'actividades_pm' => 'nullable|string',
            'firma_encargado' => 'nullable|string', // Ruta a la imagen de la firma
            'observaciones' => 'nullable|string',
            'total_jaulas' => 'nullable|integer|min:0',
            'dimension_jaulas' => 'nullable|string',
            // Validación para los buzos
            'buzo_1_id' => 'nullable|exists:trabajadores,id',
            'buzo_2_id' => 'nullable|exists:trabajadores,id',   
            'buzo_3_id' => 'nullable|exists:trabajadores,id',
            'buzo_4_id' => 'nullable|exists:trabajadores,id',
            'buzo_5_id' => 'nullable|exists:trabajadores,id',
        ]);
    }


}
