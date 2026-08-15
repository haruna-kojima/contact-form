<?php

use App\Http\Controllers\TagController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth')->group(function () {
    Route::get('/admin', [TagController::class, 'index'])->name('admin.index');
});

