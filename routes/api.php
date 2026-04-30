<?php

declare(strict_types=1);

use App\Http\Controllers\CommentController;
use App\Http\Controllers\MessageController;
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

Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::controller(CommentController::class)->group(function () {
        Route::post('/{post}', 'store')->name('comment.store');
        Route::put('/{comment}', 'edit')->name('comment.edit');
    });

    Route::get('/message/{chat}', [MessageController::class, 'index'])->name('message.index');
});
