<?php

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

Route::post('login', [\App\Http\Controllers\api\AuthController::class, 'login']);
Route::post('register', [\App\Http\Controllers\api\UserController::class, 'register']);

// User Routes
// Admin (Tidak bisa diakses member) (Access Control 0)
Route::group(['middleware' => ['auth:api', 'admin'], 'prefix' => '0'], function () {
    Route::get('users', [\App\Http\Controllers\api\UserController::class, 'index']);
});

// Member (Admin juga bisa akses route ini) (Access Control 1)
Route::group(['middleware' => ['auth:api'], 'prefix' => '1'], function () {
    Route::get('profile', [\App\Http\Controllers\api\UserController::class, 'showProfile']);
    Route::put('profile', [\App\Http\Controllers\api\UserController::class, 'editProfile']);
    Route::put('change-password', [\App\Http\Controllers\api\UserController::class, 'changePassword']);

    // Post
    Route::get('posts', [\App\Http\Controllers\api\PostController::class, 'index']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
