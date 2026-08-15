<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/tags', [AdminController::class, 'store'])->name('admin.tags.store');
    Route::delete('/admin/tags/{tag}', [AdminController::class, 'destroy'])->name('admin.tags.destroy');
    Route::get('/admin/tags/{tag}/edit', [AdminController::class, 'edit'])->name('admin.tags.edit');
    Route::put('/admin/tags/{tag}', [AdminController::class, 'update'])->name('admin.tags.update');
});

Route::get('/', [ContactController::class, 'index'])->name('contact.index');

Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');

Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');

Route::post('/contacts', [ContactController::class, 'store'])->name('contact.store');

Route::delete('/admin/tags/{tag}', [AdminController::class, 'destroy'])->name('admin.tags.destroy');

Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroyContact'])->name('admin.contacts.destroy');


