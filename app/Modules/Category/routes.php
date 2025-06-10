<?php
// app/Modules/Category/routes.php

use Illuminate\Support\Facades\Route;
use App\Modules\Category\Controllers\CategoryController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
        ->name('categories.toggle-status');
});