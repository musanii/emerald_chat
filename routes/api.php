<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/departments/{department}', [DepartmentController::class, 'show']);

    Route::get('/channels', [ChannelController::class, 'index']);
    Route::post('/channels', [ChannelController::class, 'store']);
    Route::post('/channels/{channel}/join', [ChannelController::class, 'join']);


    // Channel Messages
    Route::get('/channels/{channel}/messages', [MessageController::class, 'index']);
    Route::post('/channels/{channel}/messages', [MessageController::class, 'store']);

    // Thread Replies
    Route::get('/channels/{channel}/messages/{message}/thread', [MessageController::class, 'thread']);
});
