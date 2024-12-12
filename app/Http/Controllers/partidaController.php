<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partida;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class partidaController extends Controller
{
    public function index()
    {
        $partidas = Partida::all(); // Obtenemos todas las partidas

        // Asegurarnos de que devolvemos el atributo num_de_partida correctamente
        return response()->json($partidas, 200);
    }

    //create
    public function store(Request $request){
        // Validar la solicitud
        $validator =Validator::make($request->all(),[
            'num_de_partida'=>'required|numeric|unique:partida,num_de_partida',
            'fecha'=>'required|date',
            'concepto'=>'required|string'
        ]);

        //Retornar si hay errorv frwevwfew
        if($validator->fails()){
            $data=[
                'message'=>'Error en la validacion de los datos',
                'errors'=>$validator->errors()
            ];
            return response()->json($data,400);
        }
        //Creamos el registro
        $partida=  Partida::create([
            'num_de_partida'=> $request['num_de_partida'],
            'fecha'=> $request['fecha'],
            'concepto'=> $request['concepto']
        ]);

        //Retornamos
        return response()->json('Partida creada', 201);

    }
    //Metodo para editar 
    public function update(Request $request, $id)
    {
        // Buscar un registro por su id 
        $partida = Partida::find($id);

        // Validar si fue encontrado 
        if (!$partida) {
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        // Validar datos enviados
        $validator = Validator::make($request->all(), [
            'concepto' => 'required|string'
        ]);

        // Retornar si hay error
        if ($validator->fails()) {
            return response()->json('Error en la validación de los datos', 400);
        }

        // Actualizar datos
        $partida->concepto = $request['concepto'];

        if (!$partida->save()) {
            return response()->json('Error al actualizar el registro', 500);
        }

        return response()->json('Modificacion exitosa', 200);
    }
    //metodo para eliminar
    public function destroy($id)
    {
        //buscar el registro por su id
        $partida = Partida::find($id);

        //validar si fue encontrado
        if(!$partida){
            $data =[
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }
        //eliminar el registro
        if(!$partida->delete()){
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
    //metodo para obtener un registro por su id
    public function show($id)
    {
        //buscar un registro por id
        $partida = Partida::find($id);

        if(!$partida){
            $data = [
                'message' => 'Registro no encontrado',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        return response()->json($partida, 200);
    }
}
