<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;

// Rota principal
Route::get('/', [HomeController::class, 'index']);

// Prefixo para rotas de admin
Route::prefix('/admin')->group(function () {
    // Rota de exibição do formulário de login
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    // Rota de envio do formulário de login
    Route::post('/login', [AdminController::class, 'loginAction']);

    // Rota para exibir o formulário de registro (com nome para a rota)
    Route::get('/register', [AdminController::class, 'register'])->name('register');
    // Rota para enviar o formulário de registro
    Route::post('/register', [AdminController::class, 'registerAction']);

    Route::get('/logout', [AdminController::class, 'logout']);

    // Rota protegida pelo middleware de autenticação
    Route::get('/', [AdminController::class, 'index'])->middleware('auth');

    Route::get('/{slug}/links', [AdminController::class, 'pageLinks']);
    Route::get('/{slug}/design', [AdminController::class, 'pageDesign']);
    Route::get('/{slug}/stats', [AdminController::class, 'pageStats']);
});

// Rota para páginas dinâmicas
Route::get('/{slug}', [PageController::class, 'index']);
