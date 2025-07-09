<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('User/Create', [
            'roles' => Role::pluck('name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email, 
            'password' => Hash::make($request->password),
        ]);

        // Asignar rol, por defecto 'user' si no se especifica
        $role = $request->input('role', 'user');
        $user->assignRole($role);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Request $request, User $user): Response
    {
        return Inertia::render('User/Edit', [
            'user' => $user,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'roles' => $user->getRoleNames(),
            'availableRoles' => Role::pluck('name'),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->has('role') && $request->user()->hasRole('admin')) {
            $user->syncRoles([$request->input('role')]);
        }

        return Redirect::route('usuarios.edit', $user)->with('status', 'Perfil actualizado.');
        }
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        }

        $user->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if (! $request->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para cambiar roles.');
        }

        $user->syncRoles([$request->input('role')]);

        return back()->with('status', 'Rol del usuario actualizado correctamente.');
    }

    public function show(Request $request, User $user): Response
    {
        return Inertia::render('User/Show', [
            'user' => $user,
            'roles' => $user->getRoleNames(),
        ]);
    }

    public function index(): Response
    {
        $users = User::with('roles')->get();

        return Inertia::render('User/Index', [
            'users' => $users,
            'authUser' => Auth::user(),
        ]);
    }
    public function drop(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para eliminar usuarios.');
        }

        $user->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado permanentemente.');
    }
}
