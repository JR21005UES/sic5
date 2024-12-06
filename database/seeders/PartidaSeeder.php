<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        DB::table('partida')->insert([
            [
                'num_de_partida' => 1,
                'fecha' => '2024-01-01',
                'concepto' => 'Apertura del ejercicio contable'
            ],
            [
                'num_de_partida' => 2,
                'fecha' => '2024-01-02',
                'concepto' => 'Por venta de mercadería al contado y al crédito'
            ],
            [
                'num_de_partida' => 3,
                'fecha' => '2024-01-03',
                'concepto' => 'Por venta al contado'
            ],
            [
                'num_de_partida' => 4,
                'fecha' => '2024-01-04',
                'concepto' => 'Por devolución de mercadería'
            ],
            [
                'num_de_partida' => 5,
                'fecha' => '2024-01-05',
                'concepto' => 'Por rebaja concedida a los clientes'
            ],
            [
                'num_de_partida' => 6,
                'fecha' => '2024-01-06',
                'concepto' => 'Compra al crédito y al contado'
            ],
            [
                'num_de_partida' => 7,
                'fecha' => '2024-01-07',
                'concepto' => 'Por pago de flete'
            ],
            [
                'num_de_partida' => 8,
                'fecha' => '2024-01-08',
                'concepto' => 'Devolución de mercadería al crédito'
            ],
            [
                'num_de_partida' => 9,
                'fecha' => '2024-01-09',
                'concepto' => 'Por rebaja concedida por los proveedores'
            ],
            [
                'num_de_partida' => 10,
                'fecha' => '2024-01-10',
                'concepto' => 'Compra de propiedad, planta y equipo'
            ],
            [
                'num_de_partida' => 11,
                'fecha' => '2024-01-11',
                'concepto' => 'Pago de alquiler de local'
            ],
            [
                'num_de_partida' => 12,
                'fecha' => '2024-01-12',
                'concepto' => 'Pago de salarios a vendedores'
            ],
            [
                'num_de_partida' => 13,
                'fecha' => '2024-01-13',
                'concepto' => 'Pago de salario al contador'
            ],
            [
                'num_de_partida' => 14,
                'fecha' => '2024-01-14',
                'concepto' => 'Provisión de cuota patronal'
            ],
            /*
            [
                'num_de_partida' => 15,
                'fecha' => '2024-01-14',
                'concepto' => 'liquidacion de IVA'
            ],
            [
                'num_de_partida' => 16,
                'fecha' => '2024-01-14',
                'concepto' => 'liquidacion de cuenta'
            ],
            [
                'num_de_partida' => 17,
                'fecha' => '2024-01-14',
                'concepto' => 'liquidacion por gastos'
            ],
            [
                'num_de_partida' => 18,
                'fecha' => '2024-01-14',
                'concepto' => 'liquidacion de rebajas y devoluciones/compras'
            ]
            ,
            [
                'num_de_partida' => 19,
                'fecha' => '2024-01-14',
                'concepto' => 'Saldar cuentas de inventario'
            ],
            [
                'num_de_partida' => 20,
                'fecha' => '2024-01-14',
                'concepto' => 'Determinar costo de venta y apertura de inventario final'
            ],
            [
                'num_de_partida' => 21,
                'fecha' => '2024-01-14',
                'concepto' => 'Determinar la utilidad bruta'
            ],
            [
                'num_de_partida' => 22,
                'fecha' => '2024-01-14',
                'concepto' => 'Identificacion de la utlididad del ejercicio'
            ],
            [
                'num_de_partida' => 23,
                'fecha' => '2024-01-14',
                'concepto' => 'Liquidacion de gastos'
            ],
            [
                'num_de_partida' => 24,
                'fecha' => '2024-01-14',
                'concepto' => 'Translado a la cuenta "utlidades" '
            ],
            [
                'num_de_partida' => 25,
                'fecha' => '2024-01-14',
                'concepto' => 'Cierre del ciclo contable'
            ]*/
            // Agrega más registros aquí según tus necesidades
        ]);
    }
}
