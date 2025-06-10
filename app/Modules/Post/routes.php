<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Post\Controllers\PostController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('posts', PostController::class);
    Route::post('posts/{post}/publish', [PostController::class, 'publish'])
        ->name('posts.publish');
});