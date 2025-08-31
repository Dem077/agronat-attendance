<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\ApiKeyMiddleware;

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

Route::apiResource('users',UserController::class)->only(['index']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware(ApiKeyMiddleware::class)->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users/supervisor', [AuthController::class, 'supervisor']);
    Route::get('/users/staffs', [AuthController::class, 'staffs']);
    Route::get('/users/department', [AuthController::class, 'department']);
    Route::get('/users/active', [AuthController::class, 'active']);
    Route::apiResource('users',UserController::class)->only(['index']);
});
Route::post('/login', [AuthController::class, 'login']);