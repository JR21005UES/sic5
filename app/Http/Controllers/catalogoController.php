<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalogo;
use Illuminate\Support\Facades\Validator;


class CatalogoController extends Controller
{
    public function index()
    {
        $cuentas = Catalogo::all();
    
        // Crea una colección vacía para acumular los resultados ordenados
        $cuentasOrdenadas = collect();
    
        // Encuentra todas las cuentas de un dígito para el nivel superior
        $cuentasUnDigito = $cuentas->filter(function ($catalogo) {
            return strlen($catalogo->codigo) == 1;
        });
    
        foreach ($cuentasUnDigito as $cuentaNivel1) {
            // Añade la cuenta de nivel 1 con los campos necesarios
            $cuentasOrdenadas->push([
                'codigo' => $cuentaNivel1->codigo,
                'nombre' => $cuentaNivel1->nombre,
                'descripcion' => $cuentaNivel1->descripcion,
                'naturaleza' => $this->getNaturalezaDescripcion($cuentaNivel1->naturaleza_id)
            ]);
    
            // Encuentra todas las subcuentas de dos dígitos que empiecen con el código de nivel 1
            $cuentasDosDigitos = $cuentas->filter(function ($catalogo) use ($cuentaNivel1) {
                return strlen($catalogo->codigo) == 2 && strpos($catalogo->codigo, (string) $cuentaNivel1->codigo) === 0;
            });
    
            foreach ($cuentasDosDigitos as $cuentaNivel2) {
                // Añade la cuenta de nivel 2 con los campos necesarios
                $cuentasOrdenadas->push([
                    'codigo' => $cuentaNivel2->codigo,
                    'nombre' => $cuentaNivel2->nombre,
                    'descripcion' => $cuentaNivel2->descripcion,
                    'naturaleza' => $this->getNaturalezaDescripcion($cuentaNivel2->naturaleza_id)
                ]);
    
                // Encuentra todas las subcuentas de tres dígitos que empiecen con el código de nivel 2
                $cuentasTresDigitos = $cuentas->filter(function ($catalogo) use ($cuentaNivel2) {
                    return strlen($catalogo->codigo) > 2 && strpos($catalogo->codigo, (string) $cuentaNivel2->codigo) === 0;
                });
    
                // Añade las cuentas de tres dígitos con los campos necesarios
                foreach ($cuentasTresDigitos as $cuentaNivel3) {
                    $cuentasOrdenadas->push([
                        'codigo' => $cuentaNivel3->codigo,
                        'nombre' => $cuentaNivel3->nombre,
                        'descripcion' => $cuentaNivel3->descripcion,
                        'naturaleza' => $this->getNaturalezaDescripcion($cuentaNivel3->naturaleza_id)
                    ]);
                }
            }
        }
    
        return response()->json($cuentasOrdenadas);
    }
    
    private function getNaturalezaDescripcion($naturalezaId)
    {
        switch ($naturalezaId) {
            case 1:
                return 'Deudor';
            case 2:
                return 'Deudor cuenta R';
            case 3:
                return 'Acreedor';
            default:
                return 'Desconocido';
        }
    }
    
    public function store(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'codigo' => 'required|numeric|unique:catalogo,codigo',
            'naturaleza_id' => 'required|exists:naturaleza,id'
        ]);

        if ($validator->fails()) {
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

        return response()->json('Cuenta creada', 201);
    }

    public function show($codigo)
    {

        $cuenta = Catalogo::where('codigo', $codigo)
        ->select('codigo', 'nombre', 'descripcion', 'naturaleza_id')
        ->first();

        if(!$cuenta) {
            return response()->json('El codigo no existe', 404);
        }
    
        $cuentaEncontrada=([
            'codigo' => $cuenta->codigo,
            'nombre' => $cuenta->nombre,
            'descripcion' => $cuenta->descripcion,
            'naturaleza' => $this->getNaturalezaDescripcion($cuenta->naturaleza_id)
        ]);
    
        return response()->json($cuentaEncontrada);
    }

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

    public function destroy($codigo)
    {
        $cuenta = Catalogo::where('codigo', $codigo)->first();
        if (!$cuenta) {
            return response()->json('El catalogo no existe', 404);
        }
        $cuenta->delete();
        return response()->json('Cuenta eliminada', 201);
    }
}