<?php

use App\Http\Controllers\UserSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('*', function (Request $request) {
    return response()->json(['message' => 'Hello, World!']);
})->name('hello');
Route::post('/session/record', [UserSessionController::class, 'record']);
Route::get('/session/replay/{sessionId}', [UserSessionController::class, 'replay']);
