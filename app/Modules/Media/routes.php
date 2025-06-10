<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Media\Controllers\MediaController;

Route::middleware(['web', 'auth', 'permission:dashboard.access'])->prefix('admin')->name('admin.')->group(function () {
    // Media CRUD
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::get('media/create', [MediaController::class, 'create'])->name('media.create');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::get('media/{media}', [MediaController::class, 'show'])->name('media.show');
    Route::get('media/{media}/edit', [MediaController::class, 'edit'])->name('media.edit');
    Route::patch('media/{media}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    
    // AJAX Routes
    Route::post('media/upload', [MediaController::class, 'upload'])
        ->name('media.upload')
        ->middleware('permission:media.upload');
    Route::delete('media/bulk-delete', [MediaController::class, 'bulkDelete'])
        ->name('media.bulk-delete')
        ->middleware('permission:media.delete');
    
    // Media Browser
    Route::get('media/browser', [MediaController::class, 'browser'])
        ->name('media.browser')
        ->middleware('permission:media.index');
});