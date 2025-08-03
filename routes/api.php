<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Protected Translation routes
    Route::prefix('translations')->group(function () {
        Route::get('/', [TranslationController::class, 'index']);
        Route::post('/', [TranslationController::class, 'store']);
        Route::put('/{id}', [TranslationController::class, 'update']);
        Route::get('/export/json', [TranslationController::class, 'export']);
    });
});
