<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\AppointmentController;
use App\Http\Controllers\admin\AppointmentStatusController;
use App\Http\Controllers\admin\ClientController;
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

Route::middleware('auth')->group(function(){
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::delete('/api/users/{user}', [UserController::class, 'delete']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);
    
    Route::get('/api/clients', [ClientController::class, 'index']);
    
    Route::get('/api/appointments-status', [AppointmentStatusController::class, 'getStatusWithCount']);
    Route::get('/api/appointments', [AppointmentController::class, 'index']);
    Route::post('/api/appointments/create', [AppointmentController::class, 'store']);
    Route::get('/api/appointments/{appointment}/edit', [AppointmentController::class, 'edit']);
    Route::put('/api/appointments/{appointment}/edit', [AppointmentController::class, 'update']);
    Route::DELETE('/api/appointments/{appointment}', [AppointmentController::class, 'destroy']);
});



Route::get('{view}', ApplicationController::class)->where('view','(.*)')->middleware('auth');
