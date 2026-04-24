<?php

declare(strict_types=1);

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
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
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.show');
Route::post('/tmp/image', [UserController::class, 'storeImage']);
Route::controller(PostController::class)->group(function () {
    Route::name('post.')->group(function () {
        Route::get('/', 'show',)->name('show');
        Route::get('/view-post/{post}', 'index')->name('index');
    });
});
Route::get('user/profile/{user:username}', [UserController::class, 'index'])->name('user.index');

Route::middleware(['auth'])->group(function () {
    Route::controller(CommentController::class)
        ->prefix('/comment')->group(function () {
            Route::post('/', 'store')->name('comment.store');
            Route::put('/{comment}', 'edit')->name('comment.edit');
            Route::delete('/{comment}', 'destroy')->name('comment.destroy');
        });
    Route::controller(UserController::class)
        ->group(function () {
            Route::get('/log-out', 'logOutMethod')->name('log-out');
            Route::get('/profile/update/', 'updateProfilePage')->name('user.update-profile');
            Route::match(['post', 'put'], '/profile/update/{user}', 'updateProfile')->name('user.edit');
            Route::delete('/profile/update{user}', 'destroy')->name('user.destroy');
          //  Route::get('/user/{user:username}', 'index')->name('user.profile');
        });

    Route::get('/chats', [ChatController::class, 'getChats'])->name('chat.index');
    Route::get('/message/user/{user}', [ChatController::class, 'show'])->name('chat.show');


    //Post routing
    Route::prefix('/post')->controller(PostController::class)->group(function () {
        Route::name('post.')->group(function () {
            Route::get('/{post?}', 'createPostPage')->name('index');
            Route::put('/{post?}', 'upsert')->name('store');
            Route::delete('/{post}', 'destroy')->name('destroy');
        });
    });
});

Route::middleware(['guest'])->controller(UserController::class)->group(function () {
    Route::name('login.')->group(function () {
        Route::inertia('/login', 'User/Login')->name('index');
        Route::post('/login', 'login')->name('post');
    });
    Route::name('register.')->group(function () {
        Route::post('/register', 'register')->name('post');
        Route::inertia('/register', 'User/Register')->name('index');
    });
});
