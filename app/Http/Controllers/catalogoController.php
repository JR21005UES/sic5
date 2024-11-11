<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalogo;
use Illuminate\Support\Facades\Validator;


class CatalogoController extends Controller
{
    // Método index para obtener todos los registros
    public function index(){
        $cuentas = Catalogo::all();
        
        // Crea una colección vacía para acumular los resultados ordenados
        $cuentasOrdenadas = collect();

        // Encuentra todas las cuentas de un dígito para el nivel superior
        $cuentasUnDigito = $cuentas->filter(function($catalogo) {
            return strlen($catalogo->codigo) == 1;
        });

        foreach ($cuentasUnDigito as $cuentaNivel1) {
            // Añade la cuenta de nivel 1 a la colección
            $cuentasOrdenadas->push($cuentaNivel1);
            
            // Encuentra todas las subcuentas de dos dígitos que empiecen con el código de nivel 1
            $cuentasDosDigitos = $cuentas->filter(function($catalogo) use ($cuentaNivel1) {
                return strlen($catalogo->codigo) == 2 && strpos($catalogo->codigo, (string)$cuentaNivel1->codigo) === 0;
            });

            foreach ($cuentasDosDigitos as $cuentaNivel2) {
                // Añade la cuenta de nivel 2 a la colección
                $cuentasOrdenadas->push($cuentaNivel2);
                
                // Encuentra todas las subcuentas de tres dígitos que empiecen con el código de nivel 2
                $cuentasTresDigitos = $cuentas->filter(function($catalogo) use ($cuentaNivel2) {
                    return strlen($catalogo->codigo) > 2 && strpos($catalogo->codigo, (string)$cuentaNivel2->codigo) === 0;
                });

                // Añade las cuentas de tres dígitos a la colección en orden
                foreach ($cuentasTresDigitos as $cuentaNivel3) {
                    $cuentasOrdenadas->push($cuentaNivel3);
                }
            }
        }
        return response()->json($cuentasOrdenadas);
    }
    //referee
    // Método store para crear un nuevo registro
    public function store(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(),[
            'nombre' => 'required|string|max:255|unique:catalogo,nombre',
            'descripcion' => 'required|string',
            'codigo' => 'required|numeric|unique:catalogo,codigo',
            'naturaleza_id' => 'required|exists:naturaleza,id'
        ]);

        if ($validator -> fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }
        // Crear un nuevo registro en la tabla catalogo
        $catalogo = Catalogo::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'codigo' => $request['codigo'],
            'naturaleza_id' => $request['naturaleza_id']
        ]);

        if (!$catalogo) {
            $data = [
                'message' => 'Error al crear el registro',
                'status' => 500
            ];
            return response()->json($data, $data['status']);
        }

        $data = [
            'catalogo' => $catalogo,
            'status' => 201
        ];

        return response()->json($data, $data['status']);
    }
//fin referecnai
    // Método show para obtener un registro por su id
    public function show($codigo)
    {
        // Buscar un registro por su id
        $catalogo = Catalogo::find($codigo);

        if (!$catalogo) {
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        return response()->json($catalogo, 200);
    }

    // Método update para actualizar un registro por su id
    public function update(Request $request, $codigo)
    {
        // Buscar un registro por su codigo
        $catalogo = Catalogo::find($codigo);
    
        if (!$catalogo) {
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }
    
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255'
        ]);
    
        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }
        
        // Actualizar el registro en la tabla catalogo
        $catalogo->nombre = $request['nombre'];
        $catalogo->descripcion = $request['descripcion'];
    
        if (!$catalogo->save()) {
            $data = [
                'message' => 'Error al actualizar el registro',
                'status' => 500
            ];
            return response()->json($data, $data['status']);
        }
    
        $data = [
            'catalogo' => $catalogo,
            'status' => 200
        ];
    
        return response()->json($data, $data['status']);
    }

    // Método destroy para eliminar un registro por su id
    public function destroy($codigo)
    {
        // Buscar un registro por su id
        $catalogo = Catalogo::find($codigo);

        if (!$catalogo) {
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        // Eliminar el registro en la tabla catalogo
        if (!$catalogo->delete()) {
            $data = [
                'message' => 'Error al eliminar el registro',
                'status' => 500
            ];
            return response()->json($data, $data['status']);
        }

        $data = [
            'message' => 'Registro eliminado',
            'status' => 200
        ];

        return response()->json($data, $data['status']);
    }
}