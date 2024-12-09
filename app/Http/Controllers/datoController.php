<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;
use App\Models\Catalogo;
use Illuminate\Support\Facades\Validator;


class datoController extends Controller
{
    //metodo index todos los 
    public function index()
    {
        //obtengo  de la tabla catalogo
        $dato = Dato::all();
        //retorna todos los registro en formato JSON
        return response()->json($dato, 200);
    }

    //metodo para crear un nuevo registro
    public function store(Request $request)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'id_catalogo' => 'required|numeric|exists:catalogo,codigo',
            'id_partida' => 'required|numeric|exists:partida,num_de_partida',
            'debe' => 'required_without:haber|numeric|nullable',
            'haber' => 'required_without:debe|numeric|nullable',
            'es_diario' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }

        // Verifica si es el primer dato ingresado para la cuenta
        $primerDato = Dato::where('id_catalogo', $request->id_catalogo)->exists();

        

        // Creando un nuevo registro en la tabla dato
        $dato = Dato::create([
            'id_catalogo' => $request['id_catalogo'],
            'id_partida' => $request['id_partida'],
            'debe' => $request['debe'],
            'haber' => $request['haber'],
            'es_diario' => $request['es_diario']
        ]);

        $data = [
            'dato' => $dato,
            'status' => 201
        ];
        return response()->json($data, $data['status']);
    }

    public function storeCierre(Request $request)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'id_catalogo' => 'required|numeric|exists:catalogo,codigo',
            'id_partida' => 'required|numeric|exists:partida,num_de_partida',
            'debe' => 'required_without:haber|numeric|nullable',
            'haber' => 'required_without:debe|numeric|nullable',
            'es_diario' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }

        // Creando un nuevo registro en la tabla dato
        $dato = Dato::create([
            'id_catalogo' => $request['id_catalogo'],
            'id_partida' => $request['id_partida'],
            'debe' => $request['debe'],
            'haber' => $request['haber'],
            'es_diario' => $request['es_diario']
        ]);

        $data = [
            'dato' => $dato,
            'status' => 201
        ];
        return response()->json($data, $data['status']);
    }

    //metodo show para obtener registro por su id

    public function show($id)
    {
        $dato = Dato::find($id);

        if (!$dato) {
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }
        return response()->json($dato, 200);
    }

    //metodo para actualizar un registro por id
    public function update(Request $request, $id)
    {
        // Buscando el registro por el id
        $dato = Dato::find($id);

        if (!$dato) {
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        // Validando la solicitud
        $validator = Validator::make($request->all(), [
            'id_catalogo' => 'required|numeric|exists:catalogo,codigo',
            'id_partida' => 'required|numeric|exists:partida,num_de_partida',
            'debe' => 'required|numeric',
            'haber' => 'required|numeric',
            'es_diario'=> 'required|boolean'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }

        // Actualizando el registro en la tabla dato
        $dato->id_catalogo = $request['id_catalogo'];
        $dato->id_partida = $request['id_partida'];
        $dato->debe = $request['debe'];
        $dato->haber = $request['haber'];
        $dato->es_diario =$request['es_diario'];

        if (!$dato->save()) {
            $data = [
                'message' => 'Error al actualizar el registro',
                'status' => 500
            ];
            return response()->json($data, $data['status']);
        }

        $data = [
            'dato' => $dato,
            'status' => 200
        ];

        return response()->json($data, $data['status']);
    }

    //Metodo para eliminar un registro por su id
    public function destroy($id)
    {
        $dato = Dato::find($id);

        if (!$dato) {
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        // Eliminando el registro de la tabla
        if (!$dato->delete()) {
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
