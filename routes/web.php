<?php

use App\Http\Controllers\SignInController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SignInController::class, "index"])->name('sign_in.index');
Route::post('/create', [SignInController::class, "create"])->name('sign_in.create');
Route::get('/fetch', [SignInController::class, 'fetch'])->name('sign_in.fetch');
Route::post('/delete', [SignInController::class, 'delete'])->name('sign_in.delete');
Route::get('/get', [SignInController::class, 'get'])->name('sign_in.get');
Route::post('/update', [SignInController::class, 'update'])->name('sign_in.update');
Route::get('/update_view/{id}', [SignInController::class, 'update_view'])->name('sign_in.update_view');

Route::post('/pdf', [SignInController::class, 'pdf'])->name('pdf');
