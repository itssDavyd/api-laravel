<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//USER_CONTROLLER
Route::post('/api/register', [\App\Http\Controllers\UserController::class, 'register']);
Route::post('/api/login', [\App\Http\Controllers\UserController::class, 'login']);

//TODO LO QUE LLEVE COMO URL DE INICIO /api/cars entra contra el controller de Car.
Route::resource('/api/cars', \App\Http\Controllers\CarController::class);


