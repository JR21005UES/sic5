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
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1109,
                'id_partida' => 1,
                'debe' => 150000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 31,
                'id_partida' => 1,
                'debe' => 0,
                'haber' => 210000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 2
            [
                'id_catalogo' => 1101,
                'id_partida' => 2,
                'debe' => 35000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1103,
                'id_partida' => 2,
                'debe' => 15000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 5101,
                'id_partida' => 2,
                'debe' => 0,
                'haber' => 44247.79,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 2,
                'debe' => 0,
                'haber' => 5752.21,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 3
            [
                'id_catalogo' => 1101,
                'id_partida' => 3,
                'debe' => 60000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 5101,
                'id_partida' => 3,
                'debe' => 0,
                'haber' => 53097.35,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 3,
                'debe' => 0,
                'haber' => 6902.65,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 4
            [
                'id_catalogo' => 45,
                'id_partida' => 4,
                'debe' => 884.96,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 4,
                'debe' => 115.04,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1103,
                'id_partida' => 4,
                'debe' => 0,
                'haber' => 1000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 5
            [
                'id_catalogo' => 45,
                'id_partida' => 5,
                'debe' => 1769.91,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2109,
                'id_partida' => 5,
                'debe' => 230.09,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1103,
                'id_partida' => 5,
                'debe' => 0,
                'haber' => 2000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            // Partida 6
            [
                'id_catalogo' => 44,
                'id_partida' => 6,
                'debe' => 100000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 6,
                'debe' => 13000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 6,
                'debe' => 0,
                'haber' => 63000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2104,
                'id_partida' => 6,
                'debe' => 0,
                'haber' => 50000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 7
            [
                'id_catalogo' => 46,
                'id_partida' => 7,
                'debe' => 1700,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 7,
                'debe' => 221,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 7,
                'debe' => 0,
                'haber' => 1921,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 8
            [
                'id_catalogo' => 2104,
                'id_partida' => 8,
                'debe' => 13560,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 53,
                'id_partida' => 8,
                'debe' => 0,
                'haber' => 12000,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 8,
                'debe' => 0,
                'haber' => 1560,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 9
            [
                'id_catalogo' => 2104,
                'id_partida' => 9,
                'debe' => 1356,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 53,
                'id_partida' => 9,
                'debe' => 0,
                'haber' => 1200,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 9,
                'debe' => 0,
                'haber' => 156,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 10
            [
                'id_catalogo' => 1201,
                'id_partida' => 10,
                'debe' => 3000,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 10,
                'debe' => 390,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 10,
                'debe' => 0,
                'haber' => 3390,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            // Partida 11
            [
                'id_catalogo' => 4202,
                'id_partida' => 11,
                'debe' => 750,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 4201,
                'id_partida' => 11,
                'debe' => 750,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1112,
                'id_partida' => 11,
                'debe' => 195,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 11,
                'debe' => 0,
                'haber' => 1695,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 12
            [
                'id_catalogo' => 4202,
                'id_partida' => 12,
                'debe' => 3500,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 12,
                'debe' => 0,
                'haber' => 3141.25,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2104,
                'id_partida' => 12,
                'debe' => 0,
                'haber' => 358.75,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 13
            [
                'id_catalogo' => 4201,
                'id_partida' => 13,
                'debe' => 500,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1101,
                'id_partida' => 13,
                'debe' => 0,
                'haber' => 448.75,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2104,
                'id_partida' => 13,
                'debe' => 0,
                'haber' => 51.25,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],

            // Partida 14
            [
                'id_catalogo' => 4202,
                'id_partida' => 14,
                'debe' => 533.75,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 4201,
                'id_partida' => 14,
                'debe' => 76.25,
                'haber' => 0,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 2104,
                'id_partida' => 14,
                'debe' => 0,
                'haber' => 610,
                'es_diario'=>1,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            //partida 15
            [
                'id_catalogo' => 2109,//iva debito fiscal
                'id_partida' => 15,
                'debe' => 12309,
                'haber' => 0,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1112,//iva credito fiscal
                'id_partida' => 15,
                'debe' => 12090,
                'haber' => 0,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 6102,//Iva
                'id_partida' => 15,
                'debe' => 0
                'haber' => 219.73,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            //partida 17
            
            [
                'id_catalogo' => 44,//compras
                'id_partida' => 17,
                'debe' => 1700,
                'haber' => 0,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 46,//gastos sobre compras
                'id_partida' => 17,
                'debe' => 1700,
                'haber' => 0,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ]
            //partida 18
            ,
            [
                'id_catalogo' => 53,//Reb dev sobre compras
                'id_partida' => 18,
                'debe' => 13200,
                'haber' => 0,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 44,//compras
                'id_partida' => 18,
                'debe' => 0,
                'haber' => 13200,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ]
            //partida 19
            ,
            [
                'id_catalogo' => 44,//compras
                'id_partida' => 19,
                'debe' => 150000,
                'haber' => 0,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 1109,//inventario inicial
                'id_partida' => 19,
                'debe' => 0,
                'haber' => 150000,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ]
            //partida 20
            ,
            [
                'id_catalogo' => 6108,//inventario final
                'id_partida' => 20,
                'debe' => 200000,
                'haber' => 0,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id_catalogo' => 44,//compras
                'id_partida' => 20,
                'debe' => 0,
                'haber' => 200000,
                'es_diario'=>0,//agregado campo para comprobar si es de diario
                'created_at' => NULL,
                'updated_at' => NULL
            ]
           


        ]);
    }
}
