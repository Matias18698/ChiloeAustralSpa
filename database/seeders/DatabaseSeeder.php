<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar al seeder de usuarios
        $this->call(UserSeeder::class);
        
        // Aquí puedes llamar a otros seeders si es necesario
        // $this->call(OtherSeeder::class);
    }
}