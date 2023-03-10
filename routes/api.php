<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScoresController;
use App\Http\Controllers\UsersController;
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

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/role', [AuthController::class, 'getRole']);
    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/user', [UsersController::class, 'store']);
    Route::get('/scores', [ScoresController::class, 'index']);
    Route::post('/score', [ScoresController::class, 'store']);
    Route::get('/score/top/{user_token}/{diff_level}', [ScoresController::class, 'search']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});