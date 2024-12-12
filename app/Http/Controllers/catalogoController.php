<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalogo;
use App\Models\Dato;
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
        // Obtener los mensajes de error y concatenarlos en un solo string
        $errores = $validator->errors()->all(); // Obtiene todos los mensajes de error
        $mensaje = implode(' ', $errores); // Combina los mensajes en un solo string
        return response()->json($mensaje, 400); // Retorna el mensaje concatenado
    }

    // Crear un nuevo registro en la tabla catalogo
    $catalogo = Catalogo::create([
        'nombre' => $request['nombre'],
        'descripcion' => $request['descripcion'],
        'codigo' => $request['codigo'],
        'naturaleza_id' => $request['naturaleza_id']
    ]);

    return response()->json('Cuenta creada exitosamente', 201);
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
        // Buscar la cuenta principal por su código
        $cuenta = Catalogo::where('codigo', $codigo)->first();

        // Validar si la cuenta existe
        if (!$cuenta) {
            return response()->json('El catálogo no existe', 404);
        }

        // Verificar si tiene subcuentas asociadas
        $tieneSubcuentas = Catalogo::where('codigo', 'like', "$codigo%")
            ->where('codigo', '!=', $codigo)
            ->exists();

        if ($tieneSubcuentas) {
            return response()->json('No se puede eliminar. La cuenta tiene subcuentas asociadas.', 400);
        }

        // Verificar si tiene movimientos asociados en la tabla 'dato'
        $tieneMovimientos = Dato::where('id_catalogo', $codigo)->exists();

        if ($tieneMovimientos) {
            return response()->json('No se puede eliminar. La cuenta tiene movimientos registrados.', 400);
        }

        // Marcar como eliminada (soft delete)
        $cuenta->delete();
        return response()->json('Cuenta eliminada', 200);
    }

}