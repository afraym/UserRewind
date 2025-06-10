<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserSessionController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::post('/session/record', [UserSessionController::class, 'record']);
Route::get('/session/replay/{sessionId}', [UserSessionController::class, 'viewReplay']);
