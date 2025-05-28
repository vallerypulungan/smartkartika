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

Route::get('/teachers', [TeacherController::class, 'index']); 
Route::post('/teachers', [TeacherController::class, 'store']);
Route::put('/teachers/{id}', [TeacherController::class, 'update']);
Route::delete('/teachers/{id}', [TeacherController::class, 'destroy']);

use App\Http\Controllers\ClassController;

Route::get('/classes', [ClassController::class, 'index']);
Route::post('/classes', [ClassController::class, 'store']);      
Route::delete('/classes/{id}', [ClassController::class, 'destroy']);

use App\Http\Controllers\ChildController;

Route::get('/children', [ChildController::class, 'index']);      
Route::post('/children', [ChildController::class, 'store']);   
Route::put('/children/{id}', [ChildController::class, 'update']);
Route::delete('/children/{id}', [ChildController::class, 'destroy']); 
Route::get('/children/{id}', [ChildController::class, 'show']); 


use App\Http\Controllers\TahunAjaranController;
Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store']);
Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index']);

use App\Http\Controllers\LaporanController;

Route::post('/laporan', [LaporanController::class, 'store']);
Route::get('/laporan', [LaporanController::class, 'index']);
Route::get('/laporan/anak/{id_child}', [LaporanController::class, 'byChild']); 
Route::get('/laporan/ortu/{id_parent}', [LaporanController::class, 'byParent']); 
Route::put('/laporan/{id}', [LaporanController::class, 'update']); 
Route::delete('/laporan/{id}', [LaporanController::class, 'destroy']);

