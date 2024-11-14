<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;
use App\Models\Partida;

class reporteController extends Controller
{
    public function reportes($instruccion,$valor)
    {
        switch ($instruccion){
            case 1:
                $reporte = $this->libDiario();
                break;
            case 2:
                $reporte = $this->libMayor();
                break;
            case 3:
                $reporte = $this->balanzaComp($this->libMayor());
                break;
            case 4:
                $reporte = $this->estadoResul($this->balanzaComp($this->libMayor()), $valor);
                break;
            case 5:
                $reporte = $this->balanceGen($this->estadoResul($this->balanzaComp($this->libMayor()), $valor),$this->balanzaComp($this->libMayor()));
                break;
            case 6:
                $reporte = $this->balanceGen($this->estadoResul($this->balanzaComp($this->libMayor()), $valor),$this->balanzaComp($this->libMayor()));
                break;
            default:
                return response()->json(['mensaje' => 'Instrucción no válida']);
                break;
        }
        return response()->json($reporte);
    }    
    public function libDiario(){
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
    
        return $resultado;
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

        foreach ($datos as $dato) {
            if ($codigoActual !== $dato->id_catalogo) {
                // Si ya hemos procesado cuentas previas, añadimos el total al final del grupo
                if ($codigoActual !== null) {
                    $resultado->push([
                        'codigo' => $codigoActual,
                        'nombre_cuenta' => $nombreCuentaActual,
                        'concepto' => 'TOTAL',
                        'debe' => $totalDebe,
                        'haber' => $totalHaber,
                        'total_deudor' => $totalDeudor,
                        'total_acreedor' => $totalAcreedor,
                        'naturaleza' => $naturaleza == 1 || $naturaleza == 2 ? 'deudor' : 'acreedor',
                    ]);
                    
                }
                // Reinicia los totales para el nuevo grupo de cuentas
                $codigoActual = $dato->id_catalogo;
                $nombreCuentaActual = $dato->catalogo->nombre;
                $naturaleza = $dato->catalogo->naturaleza_id;
                $totalDebe = 0;
                $totalHaber = 0;

                // Añade el nombre de la cuenta al inicio del grupo
                $resultado->push([
                    'codigo' => $dato->catalogo->codigo,
                    'nombre_cuenta' => $dato->catalogo->nombre,
                ]);
            }

            // Agrega el movimiento al resultado mostrando el concepto de la partida
            $resultado->push([
                'codigo' => $dato->catalogo->codigo, 
                'numero_partida' => $dato->partida->num_de_partida,
                'debe' => $dato->debe,
                'haber' => $dato->haber,
                'concepto' => $dato->partida->concepto,
            ]);

            // Suma los valores de debe y haber
            $totalDebe += $dato->debe;
            $totalHaber += $dato->haber;
            $nombreCuentaActual = $dato->catalogo->nombre;            
            $naturaleza = $dato->catalogo->naturaleza_id;
            $totalDeudor = ($naturaleza == 1 || $naturaleza == 2) ? $totalDebe - $totalHaber : null;
            $totalAcreedor = ($naturaleza == 3) ? $totalHaber - $totalDebe : null;
        }

        // Añadir el último total al final del último grupo
        if ($codigoActual !== null) {
            $resultado->push([
                'codigo' => $codigoActual,
                'nombre_cuenta' => $nombreCuentaActual,
                'concepto' => 'TOTAL',
                'debe' => $totalDebe,
                'haber' => $totalHaber,
                'total_deudor' => $totalDeudor,
                'total_acreedor' => $totalAcreedor, 
                'naturaleza' => $naturaleza == 1 || $naturaleza == 2 ? 'deudor' : 'acreedor',
                   
            ]);
        }
        $this->libroMayor = $resultado->toArray(); // Guarda el resultado en una propiedad
        return $this->libroMayor;
    }
    public function balanzaComp($mayor)
    {
        $resultado = collect();
        for ($i = 0; $i < count($mayor); $i++) {
            $nombreCuenta = "";
            $totalDebe = 0;
            $totalHaber = 0;
            $totalDeudor = 0;
            $totalAcreedor = 0;
            $naturaleza = 100;

            //verifica que su oncepto sea TOTAL
           // Verifica si el índice 'concepto' existe y si su valor es 'TOTAL'
            if (isset($mayor[$i]['concepto']) && $mayor[$i]['concepto'] === 'TOTAL') {
                $codigo = $mayor[$i]['codigo'] ?? '';
                $nombreCuenta = $mayor[$i]['nombre_cuenta'] ?? '';
                $totalDebe = $mayor[$i]['debe'] ?? 0;
                $totalHaber = $mayor[$i]['haber'] ?? 0;
                $totalDeudor = $mayor[$i]['total_deudor'] ?? 0;
                $totalAcreedor = $mayor[$i]['total_acreedor'] ?? 0;
                $naturaleza = $mayor[$i]['naturaleza'] ?? 0;
                
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

        }
        $this->balanzaComp = $resultado->toArray(); // Guarda el resultado en una propiedad
        return $this->balanzaComp;
    }
    public function estadoResul($balComp, $invFinal)
    {
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
        
        
        ///aca epiezo
        $inventario = $this->buscarCuenta(1109, $balComp);
        $resultado->push([
            'nombre_cuenta' => $inventario['nombre_cuenta'] ?? 'Cuenta no encontrada',
            'total' => $inventario['total'] ?? 0,         ]);

        $mercDisponible = $comprasNetas + $inventario['total'];
        $resultado->push([
            'nombre_cuenta' => "MERCADERIA DISPONIBLE",
            'total' => $mercDisponible ?? 0,         ]);

            //INVENTARIO
        $resultado->push(values: [
            'nombre_cuenta' => "INVENTARIO FINAL",
            'total' => (float) ($invFinal ?? 0), // Convierte el total a float o usa 0 si no se encuentra
        ]);
        //costo de venta
        $costoVenta = $mercDisponible - $invFinal;
        $resultado->push([
            'nombre_cuenta' => "COSTO DE VENTAS",
            'total' => $costoVenta ?? 0,         ]);
        //UTILIDAD BRUTA
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
        $aux1 = $libMayor[5];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_deudor"],
        ]);
        $aux2 = $estadoResult[10];
        $resultado->push([
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total"],
        ]);
        $aux3 = $libMayor[6];
        $resultado->push([
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => $aux3["total_deudor"],
        ]);
        $aux4 = $libMayor[8];
        $resultado->push([
            'nombre_cuenta' => $aux4["nombre_cuenta"],
            'total' => $aux4["total_deudor"],
        ]);
        $totalActivoCorriente = $aux1["total_deudor"] + $aux2["total"] + $aux3["total_deudor"] + $aux4["total_deudor"];
        $resultado->push([
            'nombre_cuenta' => "ACTIVO CORRIENTE",
            'total' => $totalActivoCorriente,
        ]);
        $aux1 = $libMayor[9];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_deudor"],
        ]);
        $totalActivoNoCorriente = $aux1["total_deudor"];
        $resultado->push([
            'nombre_cuenta' => "ACTIVO NO CORRIENTE",
            'total' => $totalActivoNoCorriente,
        ]);
        $aux1 = $libMayor[11];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_acreedor"],
        ]);
        $aux2 = $libMayor[10];
        $resultado->push([
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total_acreedor"],
        ]);
        $aux3 = $estadoResult[19];
        $resultado->push([
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => round($aux3["total"],2),
        ]);
        $totalPasivoCorriente = $aux1["total_acreedor"] + $aux2["total_acreedor"] + $aux3["total"];
        $resultado->push([
            'nombre_cuenta' => "PASIVO CORRIENTE",
            'total' => $totalPasivoCorriente,
        ]);
        $aux1 = $libMayor[0];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_acreedor"],
        ]);
        $aux2 = $estadoResult[17];
        $resultado->push([
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total"],
        ]);
        $aux3 = $estadoResult[20];
        $resultado->push([
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => $aux3["total"],
        ]);
        $totalPasivoNoCorriente = $aux1["total_acreedor"] + $aux2["total"] + $aux3["total"];
        $resultado->push([
            'nombre_cuenta' => "CAPITAL CONTABLE",
            'total' => $totalPasivoNoCorriente,
        ]);
        $totalActivo = $totalActivoCorriente + $totalActivoNoCorriente;
        $totalActivo = round($totalActivo,2);
        $resultado->push([
            'nombre_cuenta' => "TOTAL ACTIVO",
            'total' => $totalActivo,
        ]);
        $totalPasivo = $totalPasivoCorriente + $totalPasivoNoCorriente;
        $totalPasivo = round($totalPasivo,2);
        $resultado->push([
            'nombre_cuenta' => "TOTAL PASIVO",
            'total' => $totalPasivo,
        ]);
        return $resultado;
    }

    public function buscarCuenta($numDCuenta, $cuentas)
    {
        foreach ($cuentas as $cuenta) {
            if ($cuenta['codigo'] == $numDCuenta) {
                // Determina la naturaleza y retorna el nombre de la cuenta con el total correcto
                $nombreCuenta = $cuenta['nombre_cuenta'];
                if ($cuenta['naturaleza'] == 'deudor') {
                    return [
                        'nombre_cuenta' => $nombreCuenta,
                        'total' => $cuenta['total_deudor'],
                    ];
                } elseif ($cuenta['naturaleza'] == 'acreedor') {
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
