<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'tipo_documento' => 'CC',
            'numero_documento' => '123456789',
            'cargo' => 'Coordinador',
            'organizacion' => 'Fundación X',
            'name' => 'Juan Organizador',
            'apellidos' => 'Pérez',
            'email' => 'organizador@example.com',
            'celular' => '3001234567',
            'password' => Hash::make('password123'),
            'rol' => 'organizador',
            
        ]);
    

    }
}
