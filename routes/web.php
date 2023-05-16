<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\DeleteController;
use App\Http\Controllers\IndexController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', IndexController::class);

Route::prefix('chat')->group(function () {
    Route::post('/query', QueryController::class)->name('query');
    Route::post('/upload', UploadController::class)->name('upload');
    Route::delete('/delete', DeleteController::class)->name('delete');
});
