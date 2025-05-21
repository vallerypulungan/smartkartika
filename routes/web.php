<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard-guru', function () {
    return 'Selamat datang, Guru!';
})->middleware('auth:teacher');

Route::get('/dashboard-ortu', function () {
    return 'Selamat datang, Orang Tua!';
})->middleware('auth:parent');




