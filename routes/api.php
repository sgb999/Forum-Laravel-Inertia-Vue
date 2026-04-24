<?php

declare(strict_types=1);

use App\Http\Controllers\CommentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
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

Route::middleware(['web','auth'])->group(function () {
    Route::controller(MessageController::class)->group(function () {
        Route::group(['prefix' => 'message'], function () {
            Route::name('message.')->group(function () {
                Route::get('/{chat}', 'index')->name('index');
                Route::post('/', 'store')->name('store');
            });
       });
    });
});
