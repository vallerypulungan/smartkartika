<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Documentation\DocumentationController;

Route::post('/documentations', [DocumentationController::class, 'store']);
Route::post('/login', [LoginController::class, 'loginApi'])->middleware('api');
Route::post('/logout', [LoginController::class, 'logoutApi']);
