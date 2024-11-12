<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;

class reporteController extends Controller
{
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
            });

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

}
