<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dato;

use Illuminate\Support\Facades\Validator;


class datoController extends Controller
{
    //metodo index todos los datos
    public function index(){
        //obtengo datos de la tabla catalogo
        $dato = Dato::all();
        //retorna todos los registro en formato JSON
        return response()->json($dato,200);
    }
//metodo para crear un nuevo registro
    public function store(Request $request){

        //validator
        $validator = Validator::make()


    }







}
