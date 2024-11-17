<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;
use App\Models\Partida;
use App\Models\Catalogo;

class reporteController extends Controller
{
      
    public function libroDiario()
    {
        $partidas = Partida::all();
        $datos = Dato::all();
        $resultado = collect();
    
        // Recorre el arreglo cuantas veces tenga partidas
        foreach ($partidas as $partida) {
            $movimientos = [];
    
            foreach ($datos as $dato) {
                if ($dato->id_partida == $partida->id) {
                    $movimientos[] = [
                        'codigo' => $dato->id_catalogo,
                        'nombre_cuenta' => $dato->catalogo->nombre,
                        'debe' => $dato->debe,
                        'haber' => $dato->haber
                    ];
                }
            }
    
            $resultado->push([
                'numero_partida' => $partida->num_de_partida,
                'movimientos' => $movimientos,
                'concepto' => $partida->concepto,
            ]);
        }
        $this->libroDiario = $resultado->toArray(); // Guarda el resultado en una propiedad
        return $this->libroDiario;
    } 
    public function libMayor()
    {
        $datos = Dato::with(['catalogo', 'partida'])
            ->where('es_diario', 1) // Filtra solo los registros con es_diario == 1
            ->orderBy('id_catalogo') // Ordena por el código de la cuenta
            ->get();

        $resultado = collect();
        $codigoActual = null;
        $totalDebe = 0;
        $totalHaber = 0;
        $totalDeudor = 0;
        $totalAcreedor = 0;
        $movimientos = [];

        foreach ($datos as $dato) {
            // Si cambiamos de cuenta, añadimos el total y reiniciamos los acumuladores
            if ($codigoActual !== $dato->id_catalogo) {
                if ($codigoActual !== null) {
                    // Añadimos el total y los movimientos al final del grupo actual
                    $resultado->push([
                        'codigo' => $codigoActual,
                        'nombre_cuenta' => $nombreCuentaActual,
                        'movimientos' => $movimientos,
                        'debe' => $totalDebe ?? 0,
                        'haber' => $totalHaber ?? 0,
                        'total_deudor' => $totalDeudor ?? 0,
                        'total_acreedor' => $totalAcreedor ?? 0,
                    ]);
                }

                // Reinicia los valores para la nueva cuenta
                $codigoActual = $dato->id_catalogo;
                $nombreCuentaActual = $dato->catalogo->nombre;
                $naturaleza = $dato->catalogo->naturaleza_id;
                $totalDebe = 0;
                $totalHaber = 0;
                $movimientos = [];
            }

            // Añadimos el movimiento al array de movimientos
            $movimientos[] = [
                'numero_partida' => $dato->partida->num_de_partida,
                'debe' => $dato->debe,
                'haber' => $dato->haber,
                'concepto' => $dato->partida->concepto,
            ];

            // Acumulamos los valores de debe y haber
            $totalDebe += $dato->debe;
            $totalHaber += $dato->haber;

            // Calcula el total deudor y acreedor
            $totalDeudor = ($naturaleza == 1 || $naturaleza == 2) ? $totalDebe - $totalHaber : null;
            $totalAcreedor = ($naturaleza == 3) ? $totalHaber - $totalDebe : null;
        }

        // Añadir el último total y movimientos al final del último grupo
        if ($codigoActual !== null) {
            $resultado->push([
                'codigo' => $codigoActual,
                'nombre_cuenta' => $nombreCuentaActual,
                'movimientos' => $movimientos,
                'debe' => $totalDebe ?? 0,
                'haber' => $totalHaber ?? 0,
                'total_deudor' => $totalDeudor ?? 0,
                'total_acreedor' => $totalAcreedor ?? 0,
            ]);
        }

        $this->libroMayor = $resultado->toArray(); // Guarda el resultado en una propiedad
        return $this->libroMayor;
    }
    public function balComp()
    {
        $mayor = $this->libMayor();
        $resultado = collect();
        for ($i = 0; $i < count($mayor); $i++) {
            $codigo = $mayor[$i]['codigo'] ?? '';
            $nombreCuenta = $mayor[$i]['nombre_cuenta'] ?? '';
            $totalDebe = round($mayor[$i]['debe'] ,2)?? 0;
            $totalHaber = round($mayor[$i]['haber'] ,2)?? 0;
            $totalDeudor = round($mayor[$i]['total_deudor'] ,2)?? 0;
            $totalAcreedor = round($mayor[$i]['total_acreedor'] ,2)?? 0;
            $naturaleza = Catalogo::where('codigo', $codigo)->value('naturaleza_id');
            
            $resultado->push([
                'codigo' => $codigo,
                'nombre_cuenta' => $nombreCuenta,
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'total_deudor' => $totalDeudor,
                'total_acreedor' => $totalAcreedor,
                'naturaleza' => $naturaleza,
            ]); 
        }
        $this->balanzaComp = $resultado->toArray(); // Guarda el resultado en una propiedad
        return $this->balanzaComp;
    }
    public function estadoResul($invFinal)
    {
        $balComp = $this->balComp();
        //Buscar una cuenta cuyo codigo sea 5101 usando el MODELO
        $resultado = collect();
        $aux1 = $this->buscarCuenta(5101, $balComp);
        $resultado->push([
            'nombre_cuenta' => $aux1['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $aux1['total'] ?? 0, 
            
        ]);
        $aux2 = $this->buscarCuenta(45, $balComp);
        $resultado->push([
            'nombre_cuenta' => $aux2['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $aux2['total'] ?? 0, 
            
        ]);
        $ventasNetas = $aux1['total'] - $aux2['total'];
        $resultado->push([
            'nombre_cuenta' => "VENTAS NETA",
            'total' => $ventasNetas ?? 0,         ]);
        $aux1 = $this->buscarCuenta(44, $balComp);
        $resultado->push([
            'nombre_cuenta' => $aux1['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $aux1['total'] ?? 0, 
            
        ]);
        $aux2 = $this->buscarCuenta(46, $balComp);
        $resultado->push([
            'nombre_cuenta' => $aux2['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $aux2['total'] ?? 0, 
            
        ]);
        $comprasTotales = $aux1['total'] + $aux2['total'];
        $resultado->push([
            'nombre_cuenta' => "COMPRAS TOTALES",
            'total' => $comprasTotales ?? 0,         ]);
        $aux1 = $this->buscarCuenta(53, $balComp);
        $resultado->push([
            'nombre_cuenta' => $aux1['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $aux1['total'] ?? 0, 
            
        ]);
        $comprasNetas = $comprasTotales- $aux1['total'];
        $resultado->push([
            'nombre_cuenta' => "COMPRAS NETAS",
            'total' => $comprasNetas ?? 0,         ]);
        $inventario = $this->buscarCuenta(1109, $balComp);
        $resultado->push([
            'nombre_cuenta' => $inventario['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $inventario['total'] ?? 0,         ]);

        $mercDisponible = $comprasNetas + $inventario['total'];
        $resultado->push([
            'nombre_cuenta' => "MERCADERIA DISPONIBLE",
            'total' => $mercDisponible ?? 0,         ]);
        $resultado->push(values: [
            'nombre_cuenta' => "INVENTARIO FINAL",
            'total' => (float) ($invFinal ?? 0), // Convierte el total a float o usa 0 si no se encuentra
        ]);
        $costoVenta = $mercDisponible - $invFinal;
        $resultado->push([
            'nombre_cuenta' => "COSTO DE VENTAS",
            'total' => $costoVenta ?? 0,         ]);
        $utilBruta = $ventasNetas - $costoVenta;
        $resultado->push([
            'nombre_cuenta' => "UTILIDAD BRUTAs",
            'total' => $utilBruta ?? 0,         ]);

        $aux1 = $this->buscarCuenta(4202, $balComp);
        $resultado->push([
            'nombre_cuenta' => $aux1['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $aux1['total'] ?? 0,         ]);
        $aux2 = $this->buscarCuenta(4201, $balComp);
        $resultado->push([
            'nombre_cuenta' => $aux2['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $aux2['total'] ?? 0,         ]);

        $costOperacion = $aux1['total'] + $aux2['total'];
        $resultado->push([
            'nombre_cuenta' => "GASTOS DE OPERACION",
            'total' => $costOperacion ?? 0,         ]);

        $utilOperacion = $utilBruta - $costOperacion;
        $resultado->push([
            'nombre_cuenta' => "COSTO DE OPERACION",
            'total' => $utilOperacion ?? 0,         ]);

        $reservaLegal = $utilOperacion * 0.07;
        $resultado->push([
            'nombre_cuenta' => "RESERVA LEGAL",
            'total' => $reservaLegal ?? 0,         ]);

        $utilsAntesImpuesto = $utilOperacion - $reservaLegal;
        $resultado->push([
            'nombre_cuenta' => "UTILIDADES ANTES DE IMPUESTO",
            'total' => $utilsAntesImpuesto ?? 0,         ]);

        $impuestoSobreRenta = $utilsAntesImpuesto * 0.25;
        $resultado->push([
            'nombre_cuenta' => "IMPUESTO SOBRE LA RENTA",
            'total' => $impuestoSobreRenta ?? 0,         ]);

        $utilsEjercicio = $utilsAntesImpuesto - $impuestoSobreRenta ;
        $resultado->push([
            'nombre_cuenta' => "UTILIDAD DEL EJERCICIO",
            'total' => round($utilsEjercicio, 2) ?? 0,         ]);    
        return $resultado;
    }
    public function balanceGen($estadoResult, $libMayor)
    {
        $resultado = collect();
        $i = 0;
        if($libMayor == null || $estadoResult == null){	
            return null;
        }
        $aux1 = $libMayor[5];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_deudor"],
        ]);
        $aux2 = $estadoResult[10];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total"],
        ]);
        $aux3 = $libMayor[6];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => $aux3["total_deudor"],
        ]);
        $aux4 = $libMayor[8];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux4["nombre_cuenta"],
            'total' => $aux4["total_deudor"],
        ]);
        $totalActivoCorriente = $aux1["total_deudor"] + $aux2["total"] + $aux3["total_deudor"] + $aux4["total_deudor"];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => "ACTIVO CORRIENTE",
            'total' => $totalActivoCorriente,
        ]);
        $aux1 = $libMayor[9];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_deudor"],
        ]);
        $totalActivoNoCorriente = $aux1["total_deudor"];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => "ACTIVO NO CORRIENTE",
            'total' => $totalActivoNoCorriente,
        ]);
        $aux1 = $libMayor[11];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_acreedor"],
        ]);
        $aux2 = $libMayor[10];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total_acreedor"],
        ]);
        $aux3 = $estadoResult[19];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => round($aux3["total"],2),
        ]);
        $totalPasivoCorriente = $aux1["total_acreedor"] + $aux2["total_acreedor"] + $aux3["total"];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => "PASIVO CORRIENTE",
            'total' => $totalPasivoCorriente,
        ]);
        $aux1 = $libMayor[0];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_acreedor"],
        ]);
        $aux2 = $estadoResult[17];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total"],
        ]);
        $aux3 = $estadoResult[20];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => $aux3["total"],
        ]);
        $totalPasivoNoCorriente = $aux1["total_acreedor"] + $aux2["total"] + $aux3["total"];
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => "CAPITAL CONTABLE",
            'total' => $totalPasivoNoCorriente,
        ]);
        $totalActivo = $totalActivoCorriente + $totalActivoNoCorriente;
        $totalActivo = round($totalActivo,2);
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => "TOTAL ACTIVO",
            'total' => $totalActivo,
        ]);
        $totalPasivo = $totalPasivoCorriente + $totalPasivoNoCorriente;
        $totalPasivo = round($totalPasivo,2);
        $i++;    
        $resultado->push([
            'num' => $i,
            'nombre_cuenta' => "TOTAL PASIVO",
            'total' => $totalPasivo,
        ]);
        return $resultado;
    }
    public function cierreEjer($balanGen)
    {
        $resultado = collect();
        $datos = collect();
        if($balanGen == null){
            return null;
        }
        $datos->push([
            'nombre_cuenta' => $balanGen[7]['nombre_cuenta'],
            'debe' => $balanGen[7]['total'],
            'haber' => 0,
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[8]['nombre_cuenta'],
            'debe' => $balanGen[8]['total'],
            'haber' => 0,
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[9]['nombre_cuenta'],
            'debe' => $balanGen[9]['total'],
            'haber' => 0,
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[11]['nombre_cuenta'],
            'debe' => $balanGen[11]['total'],
            'haber' => 0,
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[12]['nombre_cuenta'],
            'debe' => $balanGen[12]['total'],
            'haber' => 0,
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[13]['nombre_cuenta'],
            'debe' => $balanGen[13]['total'],
            'haber' => 0,
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[0]['nombre_cuenta'],
            'debe' => 0,
            'haber' => $balanGen[0]['total'],
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[1]['nombre_cuenta'],
            'debe' => 0,
            'haber' => $balanGen[1]['total'],
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[2]['nombre_cuenta'],
            'debe' => 0,
            'haber' => $balanGen[2]['total'],
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[3]['nombre_cuenta'],
            'debe' => 0,
            'haber' => $balanGen[3]['total'],
        ]);
        $datos->push([
            'nombre_cuenta' => $balanGen[5]['nombre_cuenta'],
            'debe' => 0,
            'haber' => $balanGen[5]['total'],
        ]);
        $totalDebe = round($balanGen[7]['total'] + $balanGen[8]['total'] + $balanGen[9]['total'] + $balanGen[11]['total'] + $balanGen[12]['total'] + $balanGen[13]['total'], 2);
        $datos->push([
            'nombre_cuenta' => "TOTAL DEL DEBE",
            'debe' => 0,
            'haber' => $totalDebe,
        ]);

        $totalHaber = round($balanGen[0]['total'] + $balanGen[1]['total'] + $balanGen[2]['total'] + $balanGen[3]['total'] + $balanGen[5]['total'], 2);
        $datos->push([
            'nombre_cuenta' => "TOTAL DEL HABER",
            'debe' => 0,
            'haber' => $totalHaber,
        ]);
        $resultado->push([
            'nombre' => "CIERRE DEL EJERCICIO",
            'Datos' =>$datos,
            'Concepto' => "/V POR CIERRE DEL EJERCICIO CONTABLE"
        ]);
        return $resultado;

    }
    public function buscarCuenta($numDCuenta, $cuentas)
    {
        foreach ($cuentas as $cuenta) {
            if ($cuenta['codigo'] == $numDCuenta) {
                $nombreCuenta = $cuenta['nombre_cuenta'];
                if ($cuenta['naturaleza'] == 1) {
                    return [
                        'nombre_cuenta' => $nombreCuenta,
                        'total' => $cuenta['total_deudor'],
                    ];
                } elseif ($cuenta['naturaleza'] == 3) {
                    return [
                        'nombre_cuenta' => $nombreCuenta,
                        'total' => $cuenta['total_acreedor'],
                    ];
                }
            }
        }
        return null;
    }
}
