<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/opdLab', function () {
    return view('opdLab');
})->name('opdLab');

Route::post('/logout', function () {
    session()->flush();
    return redirect('/');
})->name('logout');

Route::get('/api/queue/{clinicId}', [QueueController::class, 'getLiveQueue'])->name('queues.fetchApi');

Route::get('/check-password', [QueueController::class, 'checkPasswordPage'])->name('password.check');
Route::post('/verify-password', [QueueController::class, 'verifyPassword'])->name('password.verify');

Route::get('/{clinicId}', [QueueController::class, 'index'])->name('queues.index');
Route::post('/{clinicId}/next/{queueNumber}', [QueueController::class, 'next'])->name('queues.next');
Route::post('/{clinicId}/previous/{queueNumber}', [QueueController::class, 'previous'])->name('queues.previous');
Route::post('/{clinicId}/reset/{queueNumber}', [QueueController::class, 'reset'])->name('queues.reset');
