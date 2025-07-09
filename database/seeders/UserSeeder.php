<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Definición de permisos por módulo
        $permissions = [
            'gastos' => ['view', 'create', 'edit', 'delete'],
            'asistencia' => ['view', 'create', 'create manual', 'edit', 'delete'],
            'trabajadores' => ['view', 'create', 'edit', 'delete'],
            'embarcaciones' => ['view', 'create', 'edit', 'delete'],
            'bitacora' => ['view', 'create', 'edit', 'delete'],
            'usuarios' => ['view', 'create', 'edit', 'delete', 'update role'],

        ];

        // Generar permisos
        $allPermissions = $this->createPermissions($permissions);

        // Crear roles
        $roles = [
            'admin' => array_values($allPermissions),
            'user' => [
                'view asistencia',
                'view trabajadores',
                'view embarcaciones',
            ],
            'supervisor' => [
                'view asistencia',
                'create asistencia',
                'view bitacora',
                'view embarcaciones',
                'create bitacora',
            ],
        ];


        $this->createRolesWithPermissions($roles);

        // Crear usuarios
        $this->createUserWithRole('Matias Admin', 'matias18698@gmail.com', 'admin');
        $this->createUserWithRole(' Admin', 'admin@example.com', 'admin');
        $this->createUserWithRole(' User', 'user@example.com', 'user');
        $this->createUserWithRole('Supervisor', 'supervisor@example.com', 'supervisor');
    }

    protected function createPermissions(array $modules): array
    {
        $allPermissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permName = "{$action} {$module}";
                Permission::firstOrCreate(['name' => $permName]);
                $allPermissions[$permName] = $permName;
            }
        }

        return $allPermissions;
    }

    protected function createRolesWithPermissions(array $roles)
    {
        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }

    protected function createUserWithRole(string $name, string $email, string $roleName)
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt('123456789')]
        );

        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
        }
    }
}
