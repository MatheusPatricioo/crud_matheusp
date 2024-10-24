<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;

// Rota principal
Route::get('/', [HomeController::class, 'index']);

// Prefixo para rotas de admin
Route::prefix('/admin')->group(function () {
    // Rota de login (sem autenticação)
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/login', [AdminController::class, 'loginAction']);


    Route::get('/register', [AdminController::class, 'register']);

    // Rota protegida pelo middleware de autenticação
    Route::get('/', [AdminController::class, 'index'])->middleware('auth');
});

// Rota para páginas dinâmicas
Route::get('/{slug}', [PageController::class, 'index']);
