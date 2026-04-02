<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'nome' => 'cliente',
                'descricao' => 'Usuário cliente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'atendente',
                'descricao' => 'Atendente do sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'admin',
                'descricao' => 'Administrador geral',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
