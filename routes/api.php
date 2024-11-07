<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/catalogo', [CatalogoController::class, 'index']);
    
    Route::post('/catalogo', [CatalogoController::class, 'store']);

    Route::get('/catalogo/{id}', [CatalogoController::class, 'show']);

    Route::put('/catalogo/{id}', [CatalogoController::class, 'update']);
    
    Route::delete('/catalogo/{id}', [CatalogoController::class, 'destroy']);
});