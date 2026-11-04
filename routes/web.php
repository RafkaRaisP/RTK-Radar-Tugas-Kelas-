<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPjController;
use App\Http\Controllers\AdminUserController;
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
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::get('/students/{nis}/check', [AuthController::class, 'checkNis'])
    ->whereNumber('nis')->middleware('throttle:60,1')->name('students.check');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Auth: forgot & reset password (via email)
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');

// Rute yang hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'toggleComplete'])->name('tasks.complete');
    Route::post('/tasks/{task}/done', [TaskController::class, 'markAsDone'])->name('tasks.done');
    Route::get('/tasks/export', [TaskController::class, 'exportPdf'])->name('tasks.export');
});

// PJ Mapel routes
Route::middleware('auth')->prefix('pj')->name('pj.')->group(function () {
    Route::get('/dashboard', [TaskController::class, 'pjDashboard'])->name('dashboard');
    Route::post('/tasks/{task}/confirm', [TaskController::class, 'confirmTask'])->name('confirm');
});

// Panel Admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    // Kelola User (akun, ban/suspend, password)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::post('/users', [AdminUserController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/send-reset', [AdminUserController::class, 'sendResetLink'])->name('users.reset');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspendUser'])->name('users.suspend');
    Route::post('/users/{user}/activate', [AdminUserController::class, 'activateUser'])->name('users.activate');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroyUser'])->name('users.destroy');

    // Kelola PJ (assignment per tahun ajaran/mapel/kelas)
    Route::get('/pj', [AdminPjController::class, 'index'])->name('pj');
    Route::post('/pj', [AdminPjController::class, 'store'])->name('pj.store');
    Route::put('/pj/{pj}', [AdminPjController::class, 'update'])->name('pj.update');
    Route::delete('/pj/{pj}', [AdminPjController::class, 'destroy'])->name('pj.destroy');

    // Tahun Ajaran
    Route::get('/academic-years', [AdminController::class, 'academicYears'])->name('academic-years');
    Route::post('/academic-years', [AdminController::class, 'storeAcademicYear'])->name('academic-years.store');
    Route::put('/academic-years/{academicYear}', [AdminController::class, 'updateAcademicYear'])->name('academic-years.update');
    Route::delete('/academic-years/{academicYear}', [AdminController::class, 'destroyAcademicYear'])->name('academic-years.destroy');

    Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
    Route::post('/tasks', [AdminController::class, 'storeTask'])->name('tasks.store');
    Route::put('/tasks/{task}', [AdminController::class, 'updateTask'])->name('tasks.update');
    Route::delete('/tasks/{task}', [AdminController::class, 'destroyTask'])->name('tasks.destroy');
    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::post('/subjects', [AdminController::class, 'storeSubject'])->name('subjects.store');
    Route::put('/subjects/{subject}', [AdminController::class, 'updateSubject'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [AdminController::class, 'destroySubject'])->name('subjects.destroy');
    Route::get('/verifications', [AdminController::class, 'verifications'])->name('verifications');
    Route::post('/tasks/{task}/confirm', [AdminController::class, 'confirmTask'])->name('confirm');
    Route::put('/tasks/{task}/unconfirm', [AdminController::class, 'unconfirmTask'])->name('unconfirm');
});
