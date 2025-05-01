<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'tipo_documento' => 'CC',
            'numero_documento' => '000000001',
            'cargo' => 'Tester',
            'organizacion' => 'Fundación Y',
            'name' => 'Test User',
            'apellidos' => 'Apellido',
            'email' => 'test@example.com',
            'celular' => '3000000000',
            'password' => bcrypt('password123'),
            'rol' => 'invitado',

        ]);

         // Llama al seeder que creaste manualmente
        $this->call([
            UserSeeder::class,
        ]);
    }
}
