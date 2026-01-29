<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserControler;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Sin autenticación)
|--------------------------------------------------------------------------
*/

// Rutas de Autenticación
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren autenticación con Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de Autenticación protegidas
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Rutas de Usuarios
    Route::prefix('users')->group(function () {
        Route::get('/', [UserControler::class, 'index']);
        Route::get('/{id}', [UserControler::class, 'show']);
        Route::put('/{id}', [UserControler::class, 'update']);
        Route::patch('/{id}', [UserControler::class, 'updatePartial']);
        Route::put('/{id}/password', [UserControler::class, 'changePassword']);
        Route::delete('/{id}', [UserControler::class, 'destroy']);
    });

    // Rutas de Productos (protegidas)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::patch('/{id}', [ProductController::class, 'updatePartial']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});
