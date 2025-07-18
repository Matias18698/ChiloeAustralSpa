<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trabajador;
use App\Models\Asistencia;
use App\Models\Embarcacion;

use Carbon\Carbon;
use Inertia\Inertia;

class AsistenciaController extends Controller
{
    /**
     * Muestra la lista de trabajadores y sus asistencias para un mes y año específicos.
     */
public function index(Request $request)
{
    $mes = $request->input('mes', Carbon::now('America/Santiago')->month);
    $año = $request->input('año', Carbon::now('America/Santiago')->year);

    $trabajadores = Trabajador::with(['embarcacion', 'asistencias' => function ($query) use ($mes, $año) {
        $query->whereMonth('fecha', $mes)
              ->whereYear('fecha', $año);
    }])->get()
      ->sortBy('nombre')
      ->map(function ($trabajador) {
          $asistencias = $trabajador->asistencias->keyBy(fn($asistencia) =>
              Carbon::parse($asistencia->fecha)->format('Y-m-d')
          )->map(fn($asistencia) => $asistencia->tipo_asistencia);

          return [
              'id' => $trabajador->id,
              'nombre' => $trabajador->nombre,
              'apellido' => $trabajador->apellido,  // Agregado para que Vue lo use
              'cargo' => $trabajador->cargo,
              'embarcacion' => $trabajador->embarcacion ? $trabajador->embarcacion->nombre : 'Sin asignar',  // Mejor mostrar "Sin asignar"
              'embarcacion_id' => $trabajador->embarcacion ? $trabajador->embarcacion->id : null,
              'asistencias' => $asistencias,
          ];
      })->values();

    return Inertia::render('Asistencia/Index', [
        'trabajadores' => $trabajadores,
        'embarcaciones' => Embarcacion::all(),
        'mes' => intval($mes),
        'año' => intval($año),
    ]);
}


    public function create()
    {
        return Inertia::render('Asistencia/Create', [
            'trabajadores' => Trabajador::all(),
            'embarcaciones' => Embarcacion::all(),
        ]);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'trabajador_id' => 'required|exists:trabajadores,id',
        'fecha' => 'required|date',
        'tipo_asistencia' => 'required|in:D,TR,L,F',
    ]);

    // Buscar si ya existe una asistencia para el trabajador en esa fecha
    $asistencia = Asistencia::where('trabajador_id', $validated['trabajador_id'])
        ->whereDate('fecha', $validated['fecha'])
        ->first();

    if ($asistencia) {
        // Si existe, la actualizamos con los nuevos datos
        $asistencia->update($validated);
    } else {
        // Si no existe, la creamos
        Asistencia::create($validated);
    }

    $fecha = Carbon::parse($validated['fecha']);

    return redirect()->route('asistencia.index', [
        'mes' => $fecha->month,
        'año' => $fecha->year,
    ])->with('success', 'Asistencia registrada correctamente');
}

    public function edit($id)
    {
        $asistencia = Asistencia::findOrFail($id);

        return Inertia::render('Asistencia/Edit', [
            'asistencia' => $asistencia,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_asistencia' => 'required|in:D,TR,L,F',
            'fecha' => 'required|date',
        ]);

        $asistencia = Asistencia::findOrFail($id);
        $asistencia->update($validated);

        $fecha = Carbon::parse($validated['fecha']);

        return redirect()->route('asistencia.index', [
            'mes' => $fecha->month,
            'año' => $fecha->year,
        ])->with('success', 'Asistencia actualizada correctamente');
    }

    public function destroy($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        $asistencia->delete();

        return redirect()->route('asistencia.index')->with('success', 'Asistencia eliminada correctamente');
    }

    public function show($id)
    {
        $asistencia = Asistencia::findOrFail($id);

        return Inertia::render('Asistencia/Show', [
            'asistencia' => $asistencia,
        ]);
    }

    public function verificarRut(Request $request)
    {
        $request->validate([
            'rut' => 'required|string|size:12',
        ]);

        $trabajador = Trabajador::where('rut', $request->rut)->first();

        return response()->json([
            'exists' => (bool) $trabajador,
            'trabajador' => $trabajador,
        ]);
    }

    public function registrarPorRut(Request $request)
    {
        $validated = $request->validate([
            'rut' => 'required|string',
            'tipo_asistencia' => 'required|in:D,TR,L,F',
        ]);

        $trabajador = Trabajador::where('rut', $validated['rut'])->first();

        if (!$trabajador) {
            return response()->json(['error' => 'Trabajador no encontrado'], 404);
        }

        Asistencia::create([
            'trabajador_id' => $trabajador->id,
            'embarcacion_id' => $trabajador->embarcacion_id,
            'fecha' => Carbon::now('America/Santiago')->toDateString(),
            'tipo_asistencia' => $validated['tipo_asistencia'],
        ]);

        return redirect()->route('asistencia.index')->with('success', 'Asistencia creada con éxito.');
    }

    public function manual()
    {
        return Inertia::render('Asistencia/Manual', [
            'trabajadores' => Trabajador::orderBy('nombre')->get(),
            'embarcaciones' => Embarcacion::orderBy('nombre')->get(),
        ]);
    }
    public function bajada()
    {
        return Inertia::render('Asistencia/Bajada', [
            'trabajadores' => Trabajador::orderBy('nombre')->get(),
            'embarcaciones' => Embarcacion::orderBy('nombre')->get(),
        ]);
    }

    function AsistenciaCron()
    {
        // Verifica TRs
        //Busca a las asistencias activas ayer
        $asistencias = Asistencia::whereDate('fecha', Carbon::yesterday())
            ->where('tipo_asistencia', 'TR')
            ->get();

        //por cada trabajador desde ayer a 21 dias antes las asistencias TR suman menos de 21, se le crea una asistencia para hoy
        $asistencias = $asistencias->filter(function ($asistencia) {
            $count = Asistencia::where('trabajador_id', $asistencia->trabajador_id)
                ->where('embarcacion_id', $asistencia->embarcacion_id)
                ->whereDate('fecha', '>=', Carbon::now('America/Santiago')->subDays(21))
                ->where('tipo_asistencia', 'TR')
                ->count();

            return $count < 21;
        });    

        $asistenciasHoy = 0;
        //creo una asistencia con los mismos datos para hoy
        foreach ($asistencias as $asistencia) {
            // Verifica si ya existe una asistencia para hoy
            $existeHoy = Asistencia::where('trabajador_id', $asistencia->trabajador_id)
                ->where('embarcacion_id', $asistencia->embarcacion_id)
                ->whereDate('fecha', Carbon::today())
                ->exists();

            if (!$existeHoy) {
                Asistencia::create([
                    'trabajador_id' => $asistencia->trabajador_id,
                    'embarcacion_id' => $asistencia->embarcacion_id,
                    'fecha' => Carbon::now('America/Santiago')->toDateString(),
                    'tipo_asistencia' => $asistencia->tipo_asistencia,
                ]);

                $asistenciasHoy++;
            }
        }

        //Verifica Ds

        dd('Asistencias registradas para hoy: ' . $asistenciasHoy);
    }
}
