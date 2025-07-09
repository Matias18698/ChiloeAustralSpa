<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gasto;
use Inertia\Inertia;

use Illuminate\Support\Facades\DB;
class GastoController extends Controller
{

public function index()
{
    $gastos = Gasto::latest()->paginate(10);

    // Agrupar por mes para el dashboard
    $gastosMensuales = DB::table('gastos')
        ->selectRaw('
            DATE_FORMAT(fecha_oc, "%Y-%m") as mes,
            SUM(valor_neto) as total_neto,
            SUM(valor_facturado) as total_facturado
        ')
        ->whereNotNull('fecha_oc')
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

    return Inertia::render('Gastos/Index', [
        'gastos' => $gastos,
        'filters' => request()->only(['search', 'estado', 'fecha_oc']),
        'gastosMensuales' => $gastosMensuales,
    ]);
}



    public function create()
    {
        return Inertia::render('Gastos/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cotizacion' => 'nullable|string',
            'centro_barco' => 'nullable|string',
            'orden_compra' => 'nullable|string',
            'fecha_oc' => 'nullable|date',
            'hes' => 'nullable|string',
            'fecha_hes' => 'nullable|date',
            'valor_neto' => 'nullable|numeric',
            'valor_facturado' => 'nullable|numeric',
            'empresa_facturar' => 'nullable|string',
            'factura' => 'nullable|string',
            'estado' => 'required|in:pendiente,pagada',
        ]);

        Gasto::create($request->all());

        return redirect()->route('gastos.index')->with('success', 'Gasto creado exitosamente.');
    }

    public function edit(Gasto $gasto)
    {
        return Inertia::render('Gastos/Edit', ['gasto' => $gasto]);
    }

    public function update(Request $request, Gasto $gasto)
    {
        $request->validate([
            'cotizacion' => 'nullable|string',
            'centro_barco' => 'nullable|string',
            'orden_compra' => 'nullable|string',
            'fecha_oc' => 'nullable|date',
            'hes' => 'nullable|string',
            'fecha_hes' => 'nullable|date',
            'valor_neto' => 'nullable|numeric',
            'valor_facturado' => 'nullable|numeric', 
            'empresa_facturar' => 'nullable|string',
            'factura' => 'nullable|string',
            'estado' => 'required|in:pendiente,pagada',
        ]);

        $gasto->update($request->all());

        return redirect()->route('gastos.index')->with('success', 'Gasto actualizado.');
    }

    public function destroy(Gasto $gasto)
    {
        $gasto->delete();

        return redirect()->back()->with('success', 'Gasto eliminado.');
    }
     /**
     * Endpoint API para obtener gastos filtrados por rango de fechas
     */public function apiIndex(Request $request)
{
    $query = Gasto::query();

    // Filtro por rango de fechas
    $rango = $request->query('rango', 'todo');
    $hoy = now();

    switch ($rango) {
        case '7dias':
            $fechaLimite = $hoy->copy()->subDays(6)->startOfDay();
            $query->where('fecha_oc', '>=', $fechaLimite);
            break;

        case '30dias':
            $fechaLimite = $hoy->copy()->subDays(29)->startOfDay();
            $query->where('fecha_oc', '>=', $fechaLimite);
            break;

        case 'mesactual':
            $fechaLimite = $hoy->copy()->startOfMonth()->startOfDay();
            $query->where('fecha_oc', '>=', $fechaLimite);
            break;

        case 'todo':
        default:
            // Sin filtro
            break;
    }

    // Búsqueda por texto
    if ($search = $request->query('search')) {
        $query->where(function($q) use ($search) {
            $q->where('centro_barco', 'like', "%{$search}%")
              ->orWhere('orden_compra', 'like', "%{$search}%")
              ->orWhere('cotizacion', 'like', "%{$search}%")
              ->orWhere('factura', 'like', "%{$search}%");
        });
    }

    // Filtrar por estado si viene
    if ($estado = $request->query('estado')) {
        $query->where('estado', $estado);
    }

    $gastos = $query->orderBy('fecha_oc', 'desc')->paginate(10);

    return response()->json($gastos);
}

public function obtenerGastos(Request $request)
{
    $gastos = Gasto::query();

    // Filtrar por rango si es necesario
    if ($request->has('rango')) {
        $rango = $request->input('rango');
        // Aplica el filtro según el rango
        // Por ejemplo:
        if ($rango === '7dias') {
            $gastos->where('fecha_oc', '>=', now()->subDays(7));
        } elseif ($rango === '30dias') {
            $gastos->where('fecha_oc', '>=', now()->subDays(30));
        } elseif ($rango === 'mesactual') {
            $gastos->whereMonth('fecha_oc', now()->month);
        }
    }

    // Obtener los datos
    $gastos = $gastos->get(['id', 'centro_barco', 'valor_neto', 'valor_facturado']);

    // Transformar los datos para la respuesta
    $gastosTransformados = $gastos->map(function ($gasto) {
        return [
            'id' => $gasto->id,
            'centro_barco' => $gasto->centro_barco,
            'valor_neto' => $gasto->valor_neto,
            'valor_facturado' => $gasto->valor_facturado,
        ];
    });

    return response()->json($gastosTransformados);
}

}
