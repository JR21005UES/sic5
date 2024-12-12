<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dato')->insert([
            // Partida 1
            [
                'id_catalogo' => 1101,
                'id_partida' => 1,
                'debe' => 60000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1109,
                'id_partida' => 1,
                'debe' => 150000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 3101,
                'id_partida' => 1,
                'debe' => 0,
                'haber' => 210000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 2
            [
                'id_catalogo' => 1101,
                'id_partida' => 2,
                'debe' => 35000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1103,
                'id_partida' => 2,
                'debe' => 15000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 5101,
                'id_partida' => 2,
                'debe' => 0,
                'haber' => 44247.79,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 2,
                'debe' => 0,
                'haber' => 5752.21,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 3
            [
                'id_catalogo' => 1101,
                'id_partida' => 3,
                'debe' => 60000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 5101,
                'id_partida' => 3,
                'debe' => 0,
                'haber' => 53097.35,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 3,
                'debe' => 0,
                'haber' => 6902.65,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 4
            [
                'id_catalogo' => 45,
                'id_partida' => 4,
                'debe' => 884.96,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 4,
                'debe' => 115.04,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1103,
                'id_partida' => 4,
                'debe' => 0,
                'haber' => 1000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 5
            [
                'id_catalogo' => 45,
                'id_partida' => 5,
                'debe' => 1769.91,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 5,
                'debe' => 230.09,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1103,
                'id_partida' => 5,
                'debe' => 0,
                'haber' => 2000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            // Partida 6
            [
                'id_catalogo' => 44,
                'id_partida' => 6,
                'debe' => 100000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 6,
                'debe' => 13000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 6,
                'debe' => 0,
                'haber' => 63000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 2104,
                'id_partida' => 6,
                'debe' => 0,
                'haber' => 50000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 7
            [
                'id_catalogo' => 46,
                'id_partida' => 7,
                'debe' => 1700,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 7,
                'debe' => 221,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 7,
                'debe' => 0,
                'haber' => 1921,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 8
            [
                'id_catalogo' => 2104,
                'id_partida' => 8,
                'debe' => 13560,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 53,
                'id_partida' => 8,
                'debe' => 0,
                'haber' => 12000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 8,
                'debe' => 0,
                'haber' => 1560,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 9
            [
                'id_catalogo' => 2104,
                'id_partida' => 9,
                'debe' => 1356,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 53,
                'id_partida' => 9,
                'debe' => 0,
                'haber' => 1200,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 9,
                'debe' => 0,
                'haber' => 156,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 10
            [
                'id_catalogo' => 1201,
                'id_partida' => 10,
                'debe' => 3000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 10,
                'debe' => 390,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 10,
                'debe' => 0,
                'haber' => 3390,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            // Partida 11
            [
                'id_catalogo' => 4202,
                'id_partida' => 11,
                'debe' => 750,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 4201,
                'id_partida' => 11,
                'debe' => 750,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 11,
                'debe' => 195,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 11,
                'debe' => 0,
                'haber' => 1695,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 12
            [
                'id_catalogo' => 4202,
                'id_partida' => 12,
                'debe' => 3500,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 12,
                'debe' => 0,
                'haber' => 3141.25,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 2104,
                'id_partida' => 12,
                'debe' => 0,
                'haber' => 358.75,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

            // Partida 13
            [
                'id_catalogo' => 4201,
                'id_partida' => 13,
                'debe' => 500,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 13,
                'debe' => 0,
                'haber' => 448.75,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],
            [
                'id_catalogo' => 2104,
                'id_partida' => 13,
                'debe' => 0,
                'haber' => 51.25,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
            ],

                    
        ]);
    }
}