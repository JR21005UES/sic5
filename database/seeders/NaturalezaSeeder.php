<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NaturalezaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('naturaleza')->insert([
            ['id' => 1, 'nombre' => "Deudor", 'es_cuenta_r' => 0, 'deudor_acreedor' => 0], //Deudor
            ['id' => 2, 'nombre' => "Deudor cuenta R", 'es_cuenta_r' => 1, 'deudor_acreedor' => 0], //Deudor Cuenta R
            ['id' => 3, 'nombre' => "Acreedor", 'es_cuenta_r' => 0, 'deudor_acreedor' => 1], //Acreedor
        ]);
    }
}

