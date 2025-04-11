<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);
Route::post('/logout', function () {
    session()->flush();
    return redirect('/');
})->name('logout');

Route::get('/api/queue/{clinicId}', [QueueController::class, 'getLiveQueue'])->name('queues.fetchApi');

Route::get('/check-password', [QueueController::class, 'checkPasswordPage'])->name('password.check');
Route::post('/verify-password', [QueueController::class, 'verifyPassword'])->name('password.verify');

Route::get('/{clinicId}', [QueueController::class, 'index'])->name('queues.index');
Route::post('/{clinicId}/next', [QueueController::class, 'next'])->name('queues.next');
Route::post('/{clinicId}/previous', [QueueController::class, 'previous'])->name('queues.previous');
Route::post('/{clinicId}/reset', [QueueController::class, 'reset'])->name('queues.reset');
