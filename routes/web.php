<?php

declare(strict_types=1);

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PostController::class, 'show'])->name('post.show');
Route::get('/view-post/{post}', [PostController::class, 'index'])->name('post.index');
Route::get('/categories', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/user/profile/{username}', [UserController::class, 'profile'])->name('user.profile');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::inertia('/login', 'user/Login')->name('login.index');
    Route::inertia('/register', 'user/Register')->name('register.index');

    Route::controller(UserController::class)->group(function () {
        Route::post('/login', 'login')->name('login.post');
        Route::post('/register', 'register')->name('register.post');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/log-out', [UserController::class, 'logOutMethod'])->name('logout');

    // Profile
    Route::controller(UserController::class)->prefix('/profile')->name('user.')->group(function () {
        Route::get('/update', 'updateProfilePage')->name('update-profile');
        Route::put('/update/{user}', 'updateProfile')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    // Posts
    Route::controller(PostController::class)->prefix('/post')->name('post.')->group(function () {
        Route::get('/{post?}', 'postPage')->name('edit');
        Route::put('/{post?}', 'upsert')->name('upsert');
        Route::delete('/{post}', 'destroy')->name('destroy');
    });

    // Comments
    Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');

    // Chats & Messages
    Route::get('/chats', [ChatController::class, 'getChats'])->name('chat.show');
    Route::get('/message/user/{user}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/message', [MessageController::class, 'store'])->name('message.store');
});
