<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('leaves/application', [LeaveController::class, 'index']);
Route::post('leaves/application/approve/{id}', [LeaveController::class, 'approve']);
Route::post('leaves/application/reject/{id}', [LeaveController::class, 'reject']);
Route::post('leaves/application/canceled/{id}', [LeaveController::class, 'cancel']);
