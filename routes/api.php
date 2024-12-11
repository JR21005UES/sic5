<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;

use App\Http\Controllers\datoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\partidaController;
use App\Http\Controllers\reporteController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Registrar un usuario
Route::post('/register', [AuthController::class, 'register']);
//Iniciar sesión
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    //Cerrar sesión
    Route::get('/logout', [AuthController::class, 'logout']);    
    //Catalogo
    //Mostrar todos los registros
    Route::get('/catalogo', [CatalogoController::class, 'index']); 
    Route::get('/partida',[partidaController::class,'index']);
});

    Route::post('/catalogo', [CatalogoController::class, 'store']);
    //Mostrar un registro específico
    Route::get('/catalogo/{codigo}', [CatalogoController::class, 'show']);
    //Actualizar un registro
    Route::put('catalogo/{codigo}', [CatalogoController::class, 'update']);
    //Eliminar un registro 
    Route::delete('/catalogo/{codigo}', [CatalogoController::class, 'destroy']);

    //Partidas
    //mostrar todos los registros 
    //Crear un nuevo registro
    Route::post('/partida',[partidaController::class,'store']);
    //Actualizar un registro por id
    Route::put('/partida/{id}',[partidaController::class,'update']);
    //Eliminar un registro por id
    Route::delete('/partida/{id}',[partidaController::class,'destroy']);
    //Buscar un registro por id
    Route::get('/partida/{id}',[partidaController::class,'show']);

    //Dato
    //mostrar todos los registros
    Route::get('/dato',[datoController::class,'index']);
    //Crear un nuevo registro
    Route::post('/dato',[datoController::class,'store']);
    //actualizar registro por id
    Route::put('/dato/{id}',[datoController::class,'update']);
    //eliminando un registro por id
    Route::delete('/dato/{id}',[datoController::class,'destroy']);
    //buscar registro por id
    Route::get('/dato/{id}',[datoController::class,'show']);

    //reportes
    Route::get('/libDiario',[reporteController::class,'libroDiario']);
    Route::get('/libMayor',[reporteController::class,'libMayor']);
    Route::get('/libMayorPS',[reporteController::class,'libMayorPostCierre']);
    Route::get('/balComp',[reporteController::class,'balComp']);
    Route::get('/estadoResul',[reporteController::class,'estadoResul']);
    Route::get('/balanceGen',[reporteController::class,'balanceGen']);
    Route::get('/cierre',[reporteController::class,'partidasDeAjuste']);
    Route::get('/balanceGenCierre',[reporteController::class,'GeneralPostCierre']);
