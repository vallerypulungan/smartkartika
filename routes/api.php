<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;


Route::post('/login', [LoginController::class, 'loginApi'])->middleware('api');
Route::post('/logout', [LoginController::class, 'logoutApi']); 