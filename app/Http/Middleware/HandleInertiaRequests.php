<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [     
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'auth.user' => fn () => $request->user()
            ? $request->user()->only('id', 'name', 'email')
            : null,
            'auth.roles' => fn () => $request->user()
            ? $request->user()->roles->map(function ($role) {
                return $role['name'];
            })->all()
            : [],
            'auth.permissions' => fn () => $request->user()
            ? $request->user()->roles->flatMap(function ($role) {
                return $role->permissions;
            })->map(function ($permission) {
                return $permission['name'];
            })->all()
            : []
        ]);
    }
}
