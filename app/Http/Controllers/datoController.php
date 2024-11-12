<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;
use Illuminate\Support\Facades\Validator;


class datoController extends Controller {
    //metodo index todos los 
    public function index(){
        //obtengo  de la tabla catalogo
        $dato = Dato::all();
        //retorna todos los registro en formato JSON
        return response()->json($dato,200);
    }

    //metodo para crear un nuevo registro
    public function store(Request $request)
    {
        //validator
        $validator = Validator::make($request->all(),[
            'id_catalogo' => 'required|numeric|exists:catalogo,codigo',
            'id_partida' => 'required|numeric|exists:partida,id',
            'debe' => 'required|numeric',
            'haber' => 'required|numeric'
        ]);
        if ($validator -> fails()) {
            $data = [
                'message' => 'Error en la validación de los ',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json( $data,$data['status']);
        }

        //creando un nuevo registro en la tabala dato

        $dato = Dato::create([
            'id_catalogo' => $request['id_catalogo'],
            'id_partida' => $request['id_partida'],
            'debe' => $request['debe'],
            'haber' => $request['haber']
        ]);
        $data = [
            'dato' => $dato,
            'status' => 201
        ];
        return response()->json($data, $data['status']);
    }

    //metodo show para obtener registro por su id

    public function show ($id)
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
    
}
