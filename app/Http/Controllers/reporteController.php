<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;

class reporteController extends Controller
{
    public function reportes($instruccion,$valor){
        switch ($instruccion){
            case 1:
                $reporte = $this->libMayor();
                break;
            case 2:
                $reporte = $this->balanzaComp($this->libMayor());
                break;
            case 3:
                $reporte = $this->estadoResul($this->balanzaComp($this->libMayor()));
                break;
            case 4:
                $reporte = $this->balanceGen();
                break;
            case 5:
                $reporte = $this->libroMayor2();
                break;
            default:
                return response()->json(['mensaje' => 'Instrucción no válida']);
                break;
        }
        /*
        foreach ($reporte as $cuenta) {
            if ($cuenta['codigo'] == '31' && $cuenta['concepto'] == 'TOTAL') {
                $data = [
                    'nombre' => $cuenta['nombre_cuenta'],
                    'total_Deudor' => $cuenta['total_deudor'],
                    'total_Acreedor' =>  $cuenta['total_acreedor'],
                    'status' => 201
                ];
               
                
                break; // Detenemos el ciclo si encontramos la cuenta
            }
        }
                */
        
        return response()->json($reporte);
    }    
    public function libMayor()
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
                        'nombre_cuenta' => $nombreCuentaActual,
                        'concepto' => 'TOTAL',
                        'debe' => $totalDebe,
                        'haber' => $totalHaber,
                        'total_deudor' => $totalDeudor,
                        'total_acreedor' => $totalAcreedor,
                    ]);
                }
                // Reinicia los totales para el nuevo grupo de cuentas
                $codigoActual = $dato->id_catalogo;
                $nombreCuentaActual = $dato->catalogo->nombre;
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

            //verifica que su oncepto sea TOTAL
           // Verifica si el índice 'concepto' existe y si su valor es 'TOTAL'
            if (isset($mayor[$i]['concepto']) && $mayor[$i]['concepto'] === 'TOTAL') {
                $codigo = $mayor[$i]['codigo'] ?? '';
                $nombreCuenta = $mayor[$i]['nombre_cuenta'] ?? '';
                $totalDebe = $mayor[$i]['debe'] ?? 0;
                $totalHaber = $mayor[$i]['haber'] ?? 0;
                $totalDeudor = $mayor[$i]['total_deudor'] ?? 0;
                $totalAcreedor = $mayor[$i]['total_acreedor'] ?? 0;
                
                $resultado->push([
                    'codigo' => $codigo,
                    'nombre_cuenta' => $nombreCuenta,
                    'total_debe' => $totalDebe,
                    'total_haber' => $totalHaber,
                    'total_deudor' => $totalDeudor,
                    'total_acreedor' => $totalAcreedor,
                ]);
            }

        }
        $this->balanzaComp = $resultado->toArray(); // Guarda el resultado en una propiedad
        return $this->balanzaComp;
    }
    public function estadoResul($balComp)
    {
        $resultado = $this->buscarCuenta('1101',$balComp);
    
        return $resultado;
    }

    public function buscarCuenta($numDCuenta,$cuentas){
        foreach ($cuentas as $cuenta) {
            if ($cuenta['codigo'] == $numDCuenta) {
                return $cuenta;
            }
        }
        return null;
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
