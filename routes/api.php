<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Documentation\DocumentationController;

Route::post('/documentations', [DocumentationController::class, 'store']);
Route::post('/login', [LoginController::class, 'loginApi'])->middleware('api');
Route::post('/logout', [LoginController::class, 'logoutApi']);
Route::get('/documentations', [DocumentationController::class, 'index']);
Route::put('/documentations/{id}', [DocumentationController::class, 'update']);
Route::delete('/documentations/{id}', [DocumentationController::class, 'destroy']);

use App\Http\Controllers\ActivityController;

Route::get('/activities', [ActivityController::class, 'index']);
Route::post('/activities', [ActivityController::class, 'store']);
Route::put('/activities/{id}', [ActivityController::class, 'update']);
Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);

use App\Http\Controllers\TeacherController;

Route::get('/teachers', [TeacherController::class, 'index']); // Menampilkan daftar guru
Route::post('/teachers', [TeacherController::class, 'store']); // Menyimpan data guru baru
Route::put('/teachers/{id}', [TeacherController::class, 'update']); // Mengupdate data guru
Route::delete('/teachers/{id}', [TeacherController::class, 'destroy']);

