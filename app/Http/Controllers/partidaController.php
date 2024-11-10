<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partida;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class partidaController extends Controller
{
    public function index(){
        $partidas = Partida::all();
         return  response()->json($partidas,200);

    }

    public function store(Request $request){
        // Validar la solicitud
        $validator =Validator::make($request->all(),[
            'num_de_partida'=>'required|numeric|unique:partida,num_de_partida',
            'fecha'=>'required|date',
            'concepto'=>'required|string'
        ]);

        //Retornar si hay error
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
        $data=[
            'message'=>'Se creo el registro con exito',
            'partida'=>$partida
        ];
        return response()->json($data,201);

    }
    //
    
}
