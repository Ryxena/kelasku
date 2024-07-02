<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\FriendshipController;


Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('auth')->controller(UserController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login')->name('login');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    });

    Route::prefix('profile')->controller(UserController::class)->group(function () {
        Route::post('/update', 'edit')->middleware('auth:sanctum');
    });

    Route::prefix('friend')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [FriendshipController::class, 'getFriendList']);
        Route::get('/pending', [FriendshipController::class, 'getPendingFriend']);
        Route::get('/find', [FriendshipController::class, 'potentialFriends']);
        Route::post('/add', [FriendshipController::class, 'addFriend']);
        Route::post('/reject', [FriendshipController::class, 'reject']);
        Route::post('/accept', [FriendshipController::class, 'accept']);
        Route::get('/detail/{id}', [FriendshipController::class, 'detail']);
        Route::post('/colek', [FriendshipController::class, 'colek']);
    });

    Route::prefix('school')->controller(SchoolController::class)->group(function () {
        Route::post('/', 'index');
        Route::post('/create', 'store')->middleware(AdminMiddleware::class);
        Route::post('/edit/{id}', 'update')->middleware(AdminMiddleware::class);
        Route::delete('/delete/{id}', 'destroy')->middleware(AdminMiddleware::class);
    });

    Route::get('whatsapp/{number}', [WhatsappController::class, 'sendMessage'])->middleware('auth:sanctum');
});
