<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;

class reporteController extends Controller
{
    protected $utilidadDelEjercicio; // Propiedad para almacenar el valor

    public function balanzaComp()
    {
        $datos = Dato::with('catalogo')
            ->get()
            ->groupBy('id_catalogo')
            ->map(function ($grupo) {
                $nombreCuenta = $grupo->first()->catalogo->nombre;
                $naturaleza = $grupo->first()->catalogo->naturaleza_id;

                $totalDebe = $grupo->sum('debe');
                $totalHaber = $grupo->sum('haber');

                // Calcula los totales deudor y acreedor en función de la naturaleza de la cuenta
                $totalDeudor = $naturaleza == 1 ? $totalDebe - $totalHaber : null;
                $totalAcreedor = $naturaleza == 3 ? $totalHaber - $totalDebe : null;

                return [
                    'nombre_cuenta' => $nombreCuenta,
                    'total_debe' => $totalDebe,
                    'total_haber' => $totalHaber,
                    'total_deudor' => $totalDeudor,
                    'total_acreedor' => $totalAcreedor,
                ];
            })->values(); // Convierte la colección en un array y elimina las claves

        // Calcular los totales generales
        $totalDebeGeneral = $datos->sum('total_debe');
        $totalHaberGeneral = $datos->sum('total_haber');
        $totalDeudorGeneral = $datos->sum('total_deudor');
        $totalAcreedorGeneral = $datos->sum('total_acreedor');

        // Agregar los totales generales al final del array
        $datos->push([
            'nombre_cuenta' => 'Total',
            'total_debe' => $totalDebeGeneral,
            'total_haber' => $totalHaberGeneral,
            'total_deudor' => $totalDeudorGeneral,
            'total_acreedor' => $totalAcreedorGeneral,
        ]);

        return response()->json($datos);
    }

    public function libroMayor1()
    {
        $datos = Dato::with('catalogo', 'partida')
            ->get()
            ->groupBy('id_catalogo')
            ->map(function ($grupo) {
                $nombreCuenta = $grupo->first()->catalogo->nombre;
                $codigo = $grupo->first()->catalogo->codigo;
                $naturaleza = $grupo->first()->catalogo->naturaleza_id;

                $totalDebe = $grupo->sum('debe');
                $totalHaber = $grupo->sum('haber');

                // Calcula los totales deudor y acreedor en función de la naturaleza de la cuenta
                $totalDeudor = $naturaleza == 1 ? $totalDebe - $totalHaber : null;
                $totalAcreedor = $naturaleza == 3 ? $totalHaber - $totalDebe : null;

                // Formatear los movimientos de cada grupo
                $movimientos = $grupo->map(function ($dato) {
                    return [
                        'concepto' => optional($dato->partida)->concepto,
                        'debe' => $dato->debe,
                        'haber' => $dato->haber,
                    ];
                });

                return [
                    'codigo' => $codigo,
                    'nombre_cuenta' => $nombreCuenta,
                    'movimientos' => $movimientos,
                    'total_debe' => $totalDebe,
                    'total_haber' => $totalHaber,
                    'total_deudor' => $totalDeudor,
                    'total_acreedor' => $totalAcreedor,
                ];
            });

        return response()->json($datos);
    }
    public function libroMayor2()
    {
        $datos = Dato::with(['catalogo', 'partida'])
            ->orderBy('id_catalogo') // Ordena por el código de la cuenta
            ->get();

        $resultado = collect();
        $codigoActual = null;
        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($datos as $dato) {
            if ($codigoActual !== $dato->id_catalogo) {
                // Si ya hemos procesado cuentas previas, añadimos el total al final del grupo
                if ($codigoActual !== null) {
                    $resultado->push([
                        'codigo' => $codigoActual,
                        'nombre_cuenta' => 'TOTAL',
                        'debe' => $totalDebe,
                        'haber' => $totalHaber,
                        'total_deudor' => $totalDeudor,
                        'total_acreedor' => $totalAcreedor,
                    ]);
                }
                // Reinicia los totales para el nuevo grupo de cuentas
                $codigoActual = $dato->id_catalogo;
                $totalDebe = 0;
                $totalHaber = 0;

                // Añade el nombre de la cuenta al inicio del grupo
                $resultado->push([
                    'codigo' => $dato->catalogo->codigo,
                    'nombre_cuenta' => $dato->catalogo->nombre,
                    'debe' => null,
                    'haber' => null,
                    'concepto' => null,
                ]);
            }

            // Agrega el movimiento al resultado mostrando el concepto de la partida
            $resultado->push([
                'codigo' => $dato->catalogo->codigo,
                'nombre_cuenta' => null,
                'debe' => $dato->debe,
                'haber' => $dato->haber,
                'concepto' => $dato->partida->concepto,
            ]);

            // Suma los valores de debe y haber
            $totalDebe += $dato->debe;
            $totalHaber += $dato->haber;

            // Calcula los totales deudor y acreedor según la naturaleza
            $naturaleza = $dato->catalogo->naturaleza_id;
            $totalDeudor = ($naturaleza == 1 || $naturaleza == 2) ? $totalDebe - $totalHaber : null;
            $totalAcreedor = ($naturaleza == 3) ? $totalHaber - $totalDebe : null;
        }

        // Añadir el último total al final del último grupo
        if ($codigoActual !== null) {
            $resultado->push([
                'codigo' => $codigoActual,
                'nombre_cuenta' => 'TOTAL',
                'debe' => $totalDebe,
                'haber' => $totalHaber,
                'total_deudor' => $totalDeudor,
                'total_acreedor' => $totalAcreedor,
            ]);
        }

        return response()->json($resultado);
    }
    public function estadoResult($InvFin)
    {
        $inventarioFinal = $InvFin;
        $resultado = collect();
        $aux1 = $this->mayorizarCuenta(5101)->original;
        $resultado->push([
            'nombre_cuenta' => 'VENTAS',
            'monto' => $aux1['total']
        ]);
        $aux2 = $this->mayorizarCuenta(45)->original;
        $resultado->push([
            'nombre_cuenta' => 'REBAJAS Y DEVOLUCIONES SOBRE VENTAS',
            'monto' => $aux2['total']
        ]);
        $aux3 = $aux1['total'] - $aux2['total'];
        $resultado->push([
            'nombre_cuenta' => 'VENTAS NETAS',
            'monto' => $aux3
        ]);
        $aux4 = $aux3; //Guardamos ventas netas para ocupar esta variable mas abajo
        $aux1 = $this->mayorizarCuenta(44)->original;
        $resultado->push([
            'nombre_cuenta' => 'COMPRAS',
            'monto' => $aux1['total']
        ]);
        $aux2 = $this->mayorizarCuenta(46)->original;
        $resultado->push([
            'nombre_cuenta' => 'GASTOS DE COMPRA',
            'monto' => $aux2['total']
        ]);
        $aux3 = $aux1['total'] + $aux2['total'];
        $resultado->push([
            'nombre_cuenta' => 'COMPRAS TOTALES',
            'monto' => $aux3
        ]);
        $aux1 = $this->mayorizarCuenta(53)->original;
        $resultado->push([
            'nombre_cuenta' => 'REBAJAS Y DEVOLUCIONES SOBRE COMPRAS',
            'monto' => $aux1['total']
        ]);
        $aux2 = $aux3 - $aux1['total'];
        $resultado->push([
            'nombre_cuenta' => 'COMPRAS NETAS',
            'monto' => $aux2
        ]);
        $aux1 = $this->mayorizarCuenta(1109)->original;
        $resultado->push([
            'nombre_cuenta' => 'INVENTARIOS',
            'monto' => $aux1['total']
        ]);
        $aux3 = $aux1['total'] + $aux2;
        $resultado->push([
            'nombre_cuenta' => 'MERCADERIA DISPONIBLE',
            'monto' => $aux3
        ]);
        $aux1 = floatval($InvFin);
        $resultado->push([
            'nombre_cuenta' => 'INVENTARIO FINAL', //ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo
            'monto' => $aux1
        ]);
        $aux2 = $aux3 - $aux1;
        $resultado->push([
            'nombre_cuenta' => 'COSTO DE VENTA',
            'monto' => $aux2
        ]);
        $aux1 = round($aux4 - $aux2, 2);
        $resultado->push([
            'nombre_cuenta' => 'UTILIDAD BRUTA',
            'monto' => $aux1
        ]);
        $aux4 = $aux1; //Guardamos utilidad bruta para ocupar esta variable mas abajo
        $aux1 = $this->mayorizarCuenta(4202)->original;
        $resultado->push([
            'nombre_cuenta' => 'GASTOS DE VENTAS',
            'monto' => $aux1['total']
        ]);
        $aux2 = $this->mayorizarCuenta(4201)->original;
        $resultado->push([
            'nombre_cuenta' => 'GASTOS DE ADMINISTRACIÓN',
            'monto' => $aux2['total']
        ]);
        $aux3 = $aux1['total'] + $aux2['total'];
        $resultado->push([
            'nombre_cuenta' => 'GASTOS DE OPERACION',
            'monto' => $aux3
        ]);
        $aux1 = $aux4 - $aux3;
        $resultado->push([
            'nombre_cuenta' => 'UTILIDAD DE OPERACION',
            'monto' => $aux1
        ]);
        $aux2 = round($aux1 * 0.07, 2);
        $resultado->push([
            'nombre_cuenta' => 'RESERVA LEGAL',
            'monto' => $aux2
        ]);
        $aux3 = round($aux1 - $aux2, 2);
        $resultado->push([
            'nombre_cuenta' => 'UTILIDAD ANTES DE IMPUESTO',
            'monto' => $aux3
        ]);
        $aux1 = round($aux3 * 0.25, 2);
        $resultado->push([
            'nombre_cuenta' => 'IMPUESTO SOBRE LA RENTA',
            'monto' => $aux1
        ]);
        $aux2 = round($aux3 - $aux1, 2);
        $resultado->push([
            'nombre_cuenta' => 'UTILIDAD DEL EJERCICIO',
            'monto' => $aux2
        ]);

        return response()->json($resultado);
    }

    public function mayorizarCuenta($id_catalogo)
    {
        $datos = Dato::with(['catalogo', 'partida'])
            ->where('id_catalogo', $id_catalogo) // Filtra por el código de la cuenta específica
            ->get();

        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($datos as $dato) {
            // Suma los valores de debe y haber
            $totalDebe += $dato->debe;
            $totalHaber += $dato->haber;
        }

        // Calcula los totales deudor y acreedor según la naturaleza
        $naturaleza = $datos->first()->catalogo->naturaleza_id ?? null;
        $totalDeudor = ($naturaleza == 1) ? $totalDebe - $totalHaber : null;
        $totalAcreedor = ($naturaleza == 3) ? $totalHaber - $totalDebe : null;

        // Retorna solo el total deudor o acreedor según la naturaleza
        if ($naturaleza == 1) {
            return response()->json(['total' => $totalDeudor]);
        } elseif ($naturaleza == 3) {
            return response()->json(['total' => $totalAcreedor]);
        } else {
            return response()->json(['mensaje' => 'La cuenta no es de tipo deudor ni acreedor']);
        }
    }

    public function balanceGeneral()
    {
        $resultado = collect(); //Creamos un arreglo al cual le almacenamos TODOS LOS DATOS


        $aux1 = $this->mayorizarCuenta(1101)->original; //Obtenemos el total de la cuenta MAYORIZADA
        $resultado->push([
            'nombre_cuenta' => 'EFECTIVO Y EQUIVALENTES DE EFECTIVO', //Ingreso el nombre de la cuenta
            'monto' => $aux1['total'] //Ingreso el monto de la cuenta
        ]);
        $aux2 = 200000;
        $resultado->push([
            'nombre_cuenta' => 'INVENTARIO FINAL',
            'monto' => $aux2 //Ingreso el monto de la cuenta
        ]);
        //esta madre da un numero negativo en POSTMAN
        $aux3 = $this->mayorizarCuenta(1103)->original;
        $resultado->push([
            'nombre_cuenta' => 'CUENTAS Y DOCUMENTOS POR COBRAR',
            'monto' => $aux3['total']  //Ingreso el monto de la cuenta
        ]);

        $aux4 = $this->mayorizarCuenta(1112)->original;
        $resultado->push([
            'nombre_cuenta' => 'IVA CREDITO FISCAL',
            'monto' => $aux4['total']  //Ingreso el monto de la cuenta
        ]);
        $totalACorriente = $aux1['total'] + $aux2 + $aux3['total'] + $aux4['total'];
        $resultado->push([
            'nombre_cuenta' => 'CORRIENTE',
            'monto' => $totalACorriente  //Ingreso el monto de la cuenta
        ]);

        //EMPIEZAN ACTIVOS NO CORRIENTES
        $aux1 = $this->mayorizarCuenta(1201)->original;
        $resultado->push([
            'nombre_cuenta' => 'PROPIEDAD PLANTA Y EQUIPO',
            'monto' => $aux1['total']  //Ingreso el monto de la cuenta
        ]);

        $totalANoCorriente = $aux1['total'];
        $resultado->push([
            'nombre_cuenta' => 'ACTIVO NO CORRIENTE',
            'monto' => $totalANoCorriente  //Ingreso el monto de la cuenta
        ]);

        ////////////TERMINAN ACIVOS, EMPIEZAN PASIVOS

        ///PASIVOS CORRIENTES
        $aux1 = $this->mayorizarCuenta(2109)->original;
        $resultado->push([
            'nombre_cuenta' => 'IVA DEBITO FISCAL',
            'monto' => $aux1['total']  //Ingreso el monto de la cuenta
        ]);

        $aux2 = $this->mayorizarCuenta(2104)->original;
        $resultado->push([
            'nombre_cuenta' => 'CUENTAS Y DOCUMENTOS POR PAGAR',
            'monto' => $aux2['total']  //Ingreso el monto de la cuenta
        ]);

        $aux3 = $this->mayorizarCuenta(2111)->original;
        $resultado->push([
            'nombre_cuenta'=> 'IMPUSTOS POR PAGAR',
            'monto' => $aux3['total'] 
        ]);

       



        return response()->json($resultado);
    }



}
