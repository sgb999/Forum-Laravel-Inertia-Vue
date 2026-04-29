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
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/tmp/image', [UserController::class, 'storeImage']);
Route::controller(PostController::class)->group(function () {
    Route::get('/', 'show' )->name('home');
    Route::get('/view-post/{post}', 'index')->name('post.show');
});
Route::get('user/profile/{username}', [UserController::class, 'profile'])->name('user.profile');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(CommentController::class)
        ->prefix('/comment')->group(function () {
            Route::delete('/{comment}', 'destroy')->name('comment.destroy');
        });
    Route::controller(UserController::class)
        ->group(function () {
            Route::get('/log-out', 'logOutMethod')->name('log-out');
            Route::get('/profile/update/', 'updateProfilePage')->name('user.update-profile');
            Route::match(['post', 'put'], '/profile/update/{user}', 'updateProfile')->name('user.edit');
            Route::delete('/profile/update/{user}', 'destroy')->name('user.destroy');
        });

    Route::get('/chats', [ChatController::class, 'getChats'])->name('chat.index');
    Route::get('/message/user/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/message', [MessageController::class, 'store'])->name('message.store');
    Route::get('/message/{chat}', [MessageController::class, 'index'])->name('message.index');

    //Post routing
    Route::prefix('/post')->controller(PostController::class)->group(function () {
        Route::get('/{post?}', 'postPage')->name('post.index');
        Route::put('/{post?}', 'upsert')->name('post.store');
        Route::delete('/{post}', 'destroy')->name('post.destroy');
    });
});

Route::middleware(['guest'])->controller(UserController::class)->group(function () {
    Route::inertia('/login', 'Login')->name('login.index');
    Route::post('/login', 'login')->name('login.post');
    Route::post('/register', 'register')->name('register.post');
    Route::inertia('/register', 'Register')->name('register.index');
});
