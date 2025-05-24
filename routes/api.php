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

use App\Http\Controllers\ClassController;

Route::get('/classes', [ClassController::class, 'index']);

use App\Http\Controllers\ChildController;
use App\Http\Controllers\TahunAjaranController;

Route::get('/children', [ChildController::class, 'index']);      // GET: daftar siswa
Route::post('/children', [ChildController::class, 'store']);     // POST: tambah siswa
Route::put('/children/{id}', [ChildController::class, 'update']); // PUT: edit siswa
Route::delete('/children/{id}', [ChildController::class, 'destroy']); // DELETE: hapus siswa

Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store']);
Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index']);

use App\Http\Controllers\LaporanController;

Route::post('/laporan', [LaporanController::class, 'store']);
Route::get('/laporan', [LaporanController::class, 'index']); // opsional: untuk list semua laporan
Route::get('/laporan/anak/{id_child}', [LaporanController::class, 'byChild']); // laporan per anak
Route::get('/laporan/ortu/{id_parent}', [LaporanController::class, 'byParent']); // laporan per ortu
Route::put('/laporan/{id}', [LaporanController::class, 'update']); // Edit laporan
Route::delete('/laporan/{id}', [LaporanController::class, 'destroy']); // Hapus laporan

