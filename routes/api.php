<?php

use App\Http\Controllers\API\ServiceCategoryController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
});

Route::get('service', [ServiceController::class, 'all']);
Route::get('categories', [ServiceCategoryController::class, 'all']);

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
