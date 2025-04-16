<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard-guru', function () {
    return 'Selamat datang, Guru!';
})->middleware('auth:teacher');

Route::get('/dashboard-ortu', function () {
    return 'Selamat datang, Orang Tua!';
})->middleware('auth:parent');
