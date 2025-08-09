<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\Classe;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/auth/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Classes endpoints
    Route::get('/classes', function () {
        return response()->json(Classe::select('id','nom','created_at','updated_at')->get());
    });
    Route::post('/classes', function (Request $request) {
        $validated = $request->validate([
            'nom' => ['required','string','max:255'],
        ]);
        $classe = Classe::create(['nom' => $validated['nom']]);
        return response()->json(['message' => 'Classe créée', 'classe' => $classe], 201);
    });
    Route::delete('/classes/{id}', function ($id) {
        $classe = Classe::findOrFail($id);
        $classe->delete();
        return response()->json(['message' => 'Classe supprimée']);
    });
});