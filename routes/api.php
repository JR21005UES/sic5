<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\partidaController;

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

    //Mostrar todos los registros
    Route::get('/catalogo', [CatalogoController::class, 'index']); 
    //Crear un nuevo registro   
    Route::post('/catalogo', [CatalogoController::class, 'store']);
    //Mostrar un registro específico
    Route::get('/catalogo/{codigo}', [CatalogoController::class, 'show']);
    //Actualizar un registro
    Route::put('catalogo/{codigo}', [CatalogoController::class, 'update']);
    //Eliminar un registro 
    Route::delete('/catalogo/{codigo}', [CatalogoController::class, 'destroy']);

    
});
//partidas
    //mostrar todos los registros 
    Route::get('/partida',[partidaController::class,'index']);
    Route::post('/partida',[partidaController::class,'store']);