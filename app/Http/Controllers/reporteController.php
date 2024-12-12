<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\datoController;
use App\Http\Controllers\partidaController;
use App\Models\Dato;
use App\Models\Partida;
use App\Models\Catalogo;
use App\Models\Reportes;

class reporteController extends Controller
{    
    public function libroDiario()
    {
        $partidas = Partida::all();
        $datos = Dato::all();
        $resultado = collect();
        $totalDebe = 0;
        $totalHaber = 0;

        // Recorre el arreglo cuantas veces tenga partidas
        foreach ($partidas as $partida) {
            $movimientos = [];

            foreach ($datos as $dato) {
                if ($dato->id_partida == $partida->id) {
                    $movimientos[] = [
                        'id' => $dato->id,
                        'es_diario' => $dato->es_diario,
                        'codigo' => $dato->id_catalogo,
                        'nombre_cuenta' => $dato->catalogo->nombre,
                        'debe' => $dato->debe,
                        'haber' => $dato->haber
                    ];
                    $totalDebe += $dato->debe;
                    $totalHaber += $dato->haber;
                }
            }

            $resultado->push([
                'numero_partida' => $partida->num_de_partida,
                'movimientos' => $movimientos,
                'concepto' => $partida->concepto,
            ]);
        }
        $resultado->push([
            'numero_partida' => 'Total',
            'movimientos' => [],
            'concepto' => "Total Debe $". $totalDebe . "  |||  Total Haber $". $totalHaber,
        ]);

        $this->libroDiario = $resultado->toArray(); // Guarda el resultado en una propiedad

        // Convertir el libro diario a JSON y luego a string
        $jsonEncode = json_encode($this->libroDiario);
        $jsonEncodeString = (string) $jsonEncode;

        $this->guardarReporte(1, $jsonEncodeString, "Libro Diario");
        $libroDiario = $this->libroDiario; // Define the variable before returning it
        return $libroDiario;
    }
    public function libMayor()
    {
        $datos = Dato::with(['catalogo', 'partida'])
            ->where('es_diario', 1) // Filtra solo los registros con es_diario == 1
            ->orderBy('id_catalogo') // Ordena por el código de la cuenta
            ->orderBy('id_partida') // Ordena por id_partida
            ->get();

        $resultado = collect();
        $codigoActual = null;
        $total=0;
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
                    $total=0;
                    $resultado->push([
                        'codigo' => $codigoActual,
                        'nombre_cuenta' => $nombreCuentaActual,
                        'movimientos' => $movimientos,
                        'debe' => round($totalDebe, 2),
                        'haber' => round($totalHaber, 2),
                        'total_deudor' => round($totalDeudor, 2),
                        'total_acreedor' => round($totalAcreedor, 2),
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
            $movimiento = [
                'numero_partida' => $dato->partida->num_de_partida,
                'debe' => $dato->debe,
                'haber' => $dato->haber,
                'concepto' => $dato->partida->concepto,
            ];
            
            if ($naturaleza == 1) {
                $total = $total + $dato->debe - $dato->haber;
                $movimiento['total'] = $total;
            }
            
            if ($naturaleza == 3) {
                $total = $total + $dato->haber - $dato->debe;
                $movimiento['total'] = $total;
            }
            
            $movimientos[] = $movimiento;

            // Acumulamos los valores de debe y haber
            $totalDebe += $dato->debe;
            $totalHaber += $dato->haber;

            // Calcula el total deudor y acreedor
            $totalDeudor = ($naturaleza == 1 || $naturaleza == 2) ? $totalDebe - $totalHaber : null;
            $totalAcreedor = ($naturaleza == 3) ? $totalHaber - $totalDebe : null;
        }

        // Añadir el último total y movimientos al final del último grupo
        if ($codigoActual !== null) {
            $total=0;
            $resultado->push([
                'codigo' => $codigoActual,
                'nombre_cuenta' => $nombreCuentaActual,
                'movimientos' => $movimientos,
                'debe' => round($totalDebe, 2),
                'haber' => round($totalHaber, 2),
                'total_deudor' => round($totalDeudor, 2),
                'total_acreedor' => round($totalAcreedor, 2),
            ]);
        }

        $this->libroMayor = $resultado->toArray(); // Guarda el resultado en una propiedad
       
        $jsonEncode = json_encode($this->libroMayor);
        $jsonEncodeString = (string) $jsonEncode;

        $this->guardarReporte(2, $jsonEncodeString, "Libro Mayor");

        $libroMayor = $this->libroMayor; // Define the variable before returning it
        return $libroMayor;
    }
    public function balComp()
    {
        $reporte = Reportes::find(2);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el Libro Mayor',404);
        }
        $mayor = json_decode($reporte->dato_rep, true);
        
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

        $jsonEncode = json_encode($this->balanzaComp);
        $jsonEncodeString = (string) $jsonEncode;

        $this->guardarReporte(3, $jsonEncodeString, "Balance de Comprobacion");
        $balanzaComp = $this->balanzaComp; // Define the variable before returning it
        return $this->balanzaComp;
    }
    public function estadoResul(Request $request)
    {
        $invFinal = $request->query('inv_final');
        $balComp = $this->libMayor();
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
       

        $this->estadoResul = $resultado->toArray(); // Guarda el resultado en una propiedad
       
        $jsonEncode = json_encode($this->estadoResul);
        $jsonEncodeString = (string) $jsonEncode;

        $this->guardarReporte(4, $jsonEncodeString, "Estado de Resultado");
        $estadoResul = $this->estadoResul; // Define the variable before returning it
        return $estadoResul;
    
    }
    public function balanceGen()
    {
        $reporte = Reportes::find(4);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el Estado de Resultado',404);
        }
        $libroDecode = json_decode($reporte->dato_rep, true);
        $reporte = Reportes::find(2);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el Libro Mayor',404);
        }
        $libMayor = json_decode($reporte->dato_rep, true);
        $resultado = collect();
        $i = 0;
        if($libMayor == null || $libroDecode == null){	
            return null;
        }
        $aux1 = $libMayor[5];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_deudor"],
        ]);
        $aux2 = $libroDecode[10];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total"],
        ]);
        $aux3 = $libMayor[4];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => $aux3["total_deudor"],
        ]);
        $aux4 = $libMayor[7];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux4["nombre_cuenta"],
            'total' => $aux4["total_deudor"],
        ]);
        $totalActivoCorriente = $aux1["total_deudor"] + $aux2["total"] + $aux3["total_deudor"] + $aux4["total_deudor"];   
        $aux1 = $libMayor[8];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_deudor"],
        ]);
        $totalActivoNoCorriente = $aux1["total_deudor"] ;   
        $aux1 = $libMayor[9];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_acreedor"],
        ]);
        $aux2 = $libMayor[10];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total_acreedor"],
        ]);
        $aux3 = $libroDecode[19];
           
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => round($aux3["total"],2),
        ]);
        $totalPasivoCorriente = $aux1["total_acreedor"] + $aux2["total_acreedor"] + $aux3["total"];   
        $aux1 = $libMayor[11];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total_acreedor"],
        ]);
        $aux2 = $libroDecode[17];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux2["nombre_cuenta"],
            'total' => $aux2["total"],
        ]);
        $aux3 = $libroDecode[20];   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux3["nombre_cuenta"],
            'total' => $aux3["total"],
        ]);
        $totalPasivoNoCorriente = $aux1["total_acreedor"] + $aux2["total"] + $aux3["total"];   
        $totalActivo = $totalActivoCorriente + $totalActivoNoCorriente;
        $totalActivo = round($totalActivo,2);   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => "TOTAL ACTIVO",
            'total' => $totalActivo,
        ]);
        $totalPasivo = $totalPasivoCorriente + $totalPasivoNoCorriente;
        $totalPasivo = round($totalPasivo,2);   
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => "TOTAL PASIVO",
            'total' => $totalPasivo,
        ]);
        
        $this->balanceGene = $resultado->toArray(); // Guarda el resultado en una propiedad
        $jsonEncode = json_encode($this->balanceGene);
        $jsonEncodeString = (string) $jsonEncode;

        $this->guardarReporte(5, $jsonEncodeString, "Balance General");
        $balanceGene = $this->balanceGene; // Define the variable before returning it
        return $balanceGene;
    }
    public function cierreEjer()
    {
        $reporte = Reportes::find(5);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el balance general',404);
        }
        $balanGen = json_decode($reporte->dato_rep, true);
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
            // Buscar la cuenta por su código
            if ($cuenta['codigo'] == $numDCuenta) {
                $nombreCuenta = $cuenta['nombre_cuenta'];

                // Buscar la naturaleza en el modelo Catalogo
                $naturaleza = Catalogo::where('codigo', $numDCuenta)->value('naturaleza_id');

                // Verificar si se encontró la naturaleza
                if ($naturaleza === null) {
                    return null; // Si no se encuentra la naturaleza, devolver null
                }

                // Verificar la naturaleza y devolver el resultado correspondiente
                if ($naturaleza == 1) { // Naturaleza deudora
                    return [
                        'nombre_cuenta' => $nombreCuenta,
                        'total' => $cuenta['total_deudor'],
                        'naturaleza' => $naturaleza,
                    ];
                } elseif ($naturaleza == 3) { // Naturaleza acreedora
                    return [
                        'nombre_cuenta' => $nombreCuenta,
                        'total' => $cuenta['total_acreedor'],
                        'naturaleza' => $naturaleza,
                    ];
                }
            }
        }

        // Retornar null si no se encuentra la cuenta
        return null;
    }
    public function guardarReporte($id, $datoRep, $descripcion)
    {
        // Busca si existe un dato con el id proporcionado
        $reporte = Reportes::find($id);

        if ($reporte) {
            // Si existe, actualiza el dato
            $reporte->dato_rep = $datoRep;
            $reporte->descripcion = $descripcion;
            $reporte->save();
        } else {
            // Si no existe, crea un nuevo dato
            $reporte = new Reportes();
            $reporte->id = $id; // Establece el id proporcionado
            $reporte->dato_rep = $datoRep;
            $reporte->descripcion = $descripcion;
            $reporte->save();
        }
    }
    public function partidasDeAjuste()
    {
        $reporte = Reportes::find(4);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el Estado de Resultado',404);
        }
        $estadoResul = json_decode($reporte->dato_rep, true);
        $reporte = Reportes::find(5);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el Balance General',404);
        }
        $balanceGeneral = json_decode($reporte->dato_rep, true);
        $libroMayor = $this->libMayor();
        $partidaController = new partidaController();
        $datoController = new datoController();

        //Partida de ajuste 1
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Liquidacion de IVA',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $this->buscarCuenta(2109, $libroMayor);
        $datoRequest = new Request([
            'id_catalogo' => 2109,
            'id_partida' => $idPartida,
            'debe' => $aux1['total'], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $aux2 = $this->buscarCuenta(1112, $libroMayor);
        $datoRequest = new Request([
            'id_catalogo' => 1112,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux2['total'],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $aux3 = $aux1['total'] - $aux2['total'];
        $datoRequest = new Request([
            'id_catalogo' => 2104,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux3,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
    
        //Partida de ajuste 2
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/liquidaciòn de rebajas y dev sobre vent',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $this->buscarCuenta(45, $libroMayor);
        $datoRequest = new Request([
            'id_catalogo' => 45,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1['total'],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 5101,
            'id_partida' => $idPartida,
            'debe' => $aux1['total'], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        
        //Partida de ajuste 3
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/por liquidacion de gastos de compras y compras totales',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $this->buscarCuenta(46, $libroMayor);
        $datoRequest = new Request([
            'id_catalogo' => 46,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1['total'],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 44,
            'id_partida' => $idPartida,
            'debe' => $aux1['total'], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        
        //Partida de ajuste 4
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Encontrar cuentas netas y liquidacion de reb y dev sobre compras',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $this->buscarCuenta(53, $libroMayor);
        $datoRequest = new Request([
            'id_catalogo' => 53,
            'id_partida' => $idPartida,
            'debe' => $aux1['total'], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 44,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1['total'],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        
        //Partida de ajuste 5
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Liquidacion de inventario inicial y sacando mercaderia para la venta',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $this->buscarCuenta(1109, $libroMayor);
        $datoRequest = new Request([
            'id_catalogo' => 1109,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1['total'],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 44,
            'id_partida' => $idPartida,
            'debe' => $aux1['total'], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);    

        //Partida de ajuste 6
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Encontrar Costo de lo vendido y Cargar el Nuevo Inventario',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $estadoResul[10];
        $datoRequest = new Request([
            'id_catalogo' => 6103,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 44,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);     

        //Partida de ajuste 7
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Liquidacion de Compras y Sacando Utilidad Bruta',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $estadoResul[11];
        $datoRequest = new Request([
            'id_catalogo' => 5101,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 44,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);      

        //Partida de ajuste 8
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Liquidacion de ventas',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $estadoResul[12];
        $datoRequest = new Request([
            'id_catalogo' => 5101,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $aux2 = $estadoResul[17];
        $datoRequest = new Request([
            'id_catalogo' => 6102,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux2["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $aux3 = $estadoResul[19];
        $datoRequest = new Request([
            'id_catalogo' => 2104,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux3["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 6101,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"] - $aux2["total"] - $aux3["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);       

        //Partida de ajuste 9
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Liquidacion de Gastos de operacion y otros productos',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $estadoResul[14];
        $datoRequest = new Request([
            'id_catalogo' => 4201,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $aux2 = $estadoResul[13];
        $datoRequest = new Request([
            'id_catalogo' => 4202,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux2["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $aux3 = $aux1["total"] + $aux2["total"];  
        $datoRequest = new Request([
            'id_catalogo' => 6101,
            'id_partida' => $idPartida,
            'debe' => $aux3, 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);      

        //Partida de ajuste 10
        // Crear una nueva partida
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/Liquidacion perdidas y ganancias y aumentando la utilidad del ejercicio',
        ]);
        $partidaController->store($partidaRequest);

        $idPartida = Partida::latest('id')->first()->id;

        $aux1 = $estadoResul[20];
        $datoRequest = new Request([
            'id_catalogo' => 3106,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $datoRequest = new Request([
            'id_catalogo' => 6101,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);

        //Partida de CIERRE 11
        $resultado = collect(); //Coleccion para la partida
        $movimientos = []; //Coleccion para los datos de la partida
        // Crear una nueva partida
        $totalDebeCierre = 0;
        $totalHaberCierre = 0;
        $partidaRequest = new Request([
            'num_de_partida' => Partida::max('num_de_partida') + 1, // Incrementar el número de partida
            'fecha' => '2021-12-31',
            'concepto' => 'V/PARTIDA DE CIERRE',
        ]);
        $i =0; //BORAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAR
        $partidaController->store($partidaRequest);
        $idPartida = Partida::latest('id')->first()->id;
        $aux1 = $balanceGeneral[6]; //IVA DEBITO FISCAL  
        $totalDebeCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 2109,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[5]; //CUENTAS Y DOCUMENTOS POR PAGAR
        $totalDebeCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 2104,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[7]; //IMPUESTO POR PAGAR
        $totalDebeCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 2111,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[8]; //CAPITAL SOCIAL
        $totalDebeCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 3101,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[9]; //RESERVA LEGAL
        $totalDebeCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 3103,
            'id_partida' => $idPartida,
            'debe' => round($aux1["total"], 2),
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[10];//UTILIDAD DEL EJERCICIO
        $totalDebeCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 3106,
            'id_partida' => $idPartida,
            'debe' => $aux1["total"], 
            'haber' => 0,
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[2]; //EFEFCTIVO Y EQUIVALENTE DE EFECTIVO
        $totalHaberCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 1101,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[1]; //iNVENTARIO
        $totalHaberCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 1109,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[0]; //CUENTAS POR COBRAR
        $totalHaberCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 1103,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[3];//IVA CREDITO FISCAL
        $totalHaberCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 1112,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $aux1 = $balanceGeneral[4]; //PROPIEDAD, PLANTA Y EQUIPO
        $totalHaberCierre += $aux1["total"];
        $datoRequest = new Request([
            'id_catalogo' => 1201,
            'id_partida' => $idPartida,
            'debe' => 0, 
            'haber' => $aux1["total"],
            'es_diario' => 0,
        ]);
        $datoController->storeCierre($datoRequest);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => $aux1['nombre_cuenta'],
            'total' => $aux1["total"],
        ]);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => "TOTAL ACTIVO",
            'total' => round($totalDebeCierre, 2)
        ]);
        $resultado->push([
            'numero' => $i++, //BORAAAAAAAA
            'nombre_cuenta' => "TOTAL PASIVO",
            'total' => round($totalHaberCierre, 2)
        ]);
        
        $CierreDelEjercicio = $resultado->toArray(); // Guarda el resultado en una propiedad

        $this->CierreDelEjercicio = $resultado->toArray(); // Guarda el resultado en una propiedad
        $jsonEncode = json_encode($this->CierreDelEjercicio);
        $jsonEncodeString = (string) $jsonEncode;
        $this->guardarReporte(6, $jsonEncodeString, "Cierre del ejercicio contable"); // Guarda el resultado en la base de datos
        $CierreDelEjercicio = $this->CierreDelEjercicio; // Define the variable before returning it
        
        return $CierreDelEjercicio;
    }
    public function GeneralPostCierre()
    {
        $reporte = Reportes::find(5);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el Estado de Resultado',404);
        }
        $balanceGeneralDecode = json_decode($reporte->dato_rep, true);
        $reporte = Reportes::find(6);
        if ($reporte == null) {
            //si no existe el reporte retorna un mensaje y un error 404
            return response()->json('No se ha generado el Libro Mayor',404);
        }
        $partidaDeCierre = json_decode($reporte->dato_rep, true);
        $resultado = collect();
        
        $aux1 = $balanceGeneralDecode[0]; //Cuentas y documentos por pagar
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[6]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[1]; //Inventario Final
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[7]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[2]; //EFECTIVO Y EQUIVALENTES DE EFECTIVO
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[8]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[3]; //IVA CREDITO FISCAL
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[9]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[4]; //PROPIEDAD, PLANTA Y EQUIPO
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[10]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[5]; //DOCUMENTOS POR PAGAR
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[1]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[6]; //IVA DEBITO FISCAL
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[0]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[7]; //IMPUESTO SOBRE LA RENTA
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[2]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[8]; //CAPITAL SOCIAL
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[3]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[9]; //RESERVA LEGAL
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[4]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[10]; //UTILIDAD DEL EJERCICIO
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[5]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[11]; //TOTAL ACTIVO
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[11]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);

        $aux1 = $balanceGeneralDecode[12]; //TOTAL PASIVO
        $aux1["total"] = $aux1["total"] - $partidaDeCierre[12]["total"];
        $resultado->push([
            'nombre_cuenta' => $aux1["nombre_cuenta"],
            'total' => $aux1["total"],
        ]);
        
        
        $this->balanceGene = $resultado->toArray(); // Guarda el resultado en una propiedad
        $jsonEncode = json_encode($this->balanceGene);
        $jsonEncodeString = (string) $jsonEncode;
        $this->guardarReporte(7, $jsonEncodeString, "Balance General Post Cierre");
        $balanceGene = $this->balanceGene; // Define the variable before returning it

        return $balanceGene;
    }  
}   