<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('/category', CategoryController::class);
Route::resource('/post', PostController::class);

Route::get('/logs', [ActivityLogController::class, 'index']);
Route::post('/logs/revert/{activityLog}', [ActivityLogController::class, 'revertToLogVersion']);
