<?php

use App\Http\Controllers\Api\HiveController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Hive availability check (used by booking modal)
Route::get('/hive/availability', [HiveController::class, 'checkAvailability']);
