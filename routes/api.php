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
    Route::post('profile', [\App\Http\Controllers\api\UserController::class, 'editProfile']);
    Route::post('change-password', [\App\Http\Controllers\api\UserController::class, 'changePassword']);

    // Post
    Route::get('posts', [\App\Http\Controllers\api\PostController::class, 'index']); // Get all posts
    Route::get('posts/{user}', [\App\Http\Controllers\api\PostController::class, 'userPosts']); // Get posts by user
    Route::post('post', [\App\Http\Controllers\api\PostController::class, 'store']); // Create new post
    Route::get('post/{post}', [\App\Http\Controllers\api\PostController::class, 'show']); // Get post by id
    Route::post('post/{post}', [\App\Http\Controllers\api\PostController::class, 'update']); // Update post by id
    Route::delete('post/{post}', [\App\Http\Controllers\api\PostController::class, 'destroy']); // Delete post by id

    // Comment
    Route::get('comments/{post}', [\App\Http\Controllers\api\CommentController::class, 'index']); // Get all comments for a post
    Route::post('comment/{post}', [\App\Http\Controllers\api\CommentController::class, 'store']); // Create new comment for a post
    Route::post('comment/{comment}/update', [\App\Http\Controllers\api\CommentController::class, 'update']); // Update comment by id
    Route::delete('comment/{comment}', [\App\Http\Controllers\api\CommentController::class, 'destroy']); // Delete comment by id
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
