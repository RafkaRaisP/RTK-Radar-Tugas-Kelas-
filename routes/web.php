<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Halaman utama — langsung arahkan ke radar publik
Route::get('/', fn () => redirect()->route('radar'));

// Radar publik: lihat semua tugas tanpa login
Route::get('/radar', [TaskController::class, 'publicIndex'])->name('radar');

// Auth: halaman login & register
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'toggleComplete'])->name('tasks.complete');
    Route::get('/tasks/export', [TaskController::class, 'exportCsv'])->name('tasks.export');
});
