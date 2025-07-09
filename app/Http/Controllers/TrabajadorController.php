<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Embarcacion;

class TrabajadorController extends Controller
{
    /**
     * Mostrar una lista de trabajadores del usuario autenticado.
     */
    public function index()
    {
        
        // Obtener trabajadores
        $trabajadores = Trabajador::all();

        return Inertia::render('Trabajador/Index', [
            'trabajadores' => $trabajadores
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo trabajador.
     */

    public function create()
    {
        return Inertia::render('Trabajador/Create', [
            'embarcaciones' => Embarcacion::all(),
        ]);
    }
    /**
     * Almacenar un nuevo trabajador en la base de datos.
     */
    public function store(Request $request)
    {
        $data = $request->except('avatar');
        
        
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->handleAvatarUpload($request);
        }

        $data['user_id'] = Auth::id(); // Asignar el ID del usuario autenticado
        
        try {
            Trabajador::create($data);
            return redirect()->route('trabajador.index')->with('success', 'Trabajador creado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear trabajador: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Hubo un problema al crear el trabajador.']);
        }
    }

    /**
     * Mostrar el formulario para editar un trabajador existente.
     */
    public function edit(Trabajador $trabajador)
    {
        return Inertia::render('Trabajador/Edit', [
            'trabajador' => $trabajador
        ]);
    }

    /**
     * Actualizar un trabajador en la base de datos.
     */
    public function update(request $request, Trabajador $trabajador)
    {
        $data = $request->except('avatar');
        
        if ($request->hasFile('avatar')) {
            // Subir el nuevo avatar y eliminar el anterior
            $data['avatar'] = $this->handleAvatarUpload($request);
            $this->deleteAvatarIfExists($trabajador);
        }
    
        // Actualizar el trabajador con los nuevos datos
        $trabajador->update($data);
    
        // Resto del código...
    }
    

    /**
     * Eliminar un trabajador de la base de datos.
     */
    public function destroy(Trabajador $trabajador)
    {
        try {
            // Eliminar el avatar si existe
            $this->deleteAvatarIfExists($trabajador);
            // Eliminar el trabajador
            $trabajador->delete();
            return redirect()->route('trabajador.index')->with('success', 'Trabajador eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar trabajador: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Hubo un problema al eliminar el trabajador.']);
        }
    }

    /**
     * Validar y subir el avatar.
     */
    private function handleAvatarUpload(Request $request)
    {
        try {
            return $request->file('avatar')->store('avatars', 'public');
        } catch (\Exception $e) {
            Log::error('Error al cargar el avatar: ' . $e->getMessage());
            return null; // Puedes lanzar un error o manejarlo según lo necesites.
        }
    }

    /**
     * Eliminar el avatar si existe.
     */
    private function deleteAvatarIfExists(Trabajador $trabajador)
    {
        if ($trabajador->avatar) {
            try {
                Storage::disk('public')->delete($trabajador->avatar);
            } catch (\Exception $e) {
                Log::error('Error al eliminar el avatar: ' . $e->getMessage());
            }
        }
    }
    

    /**
     * Mostrar detalles de un trabajador.
     */
    public function show(Trabajador $trabajador)
    {
        return Inertia::render('Trabajador/Show', [
            'trabajador' => $trabajador
        ]);
    }

    
}
