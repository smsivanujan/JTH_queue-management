<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OPDLabController;
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

Route::get('/opd-lab', [OPDLabController::class, 'index'])->name('opd.lab');
Route::get('/opd-lab/second-screen', [OPDLabController::class, 'secondScreen'])->name('opd.lab.second-screen');


Route::get('/api/queue/{clinicId}', [QueueController::class, 'getLiveQueue'])->name('queues.fetchApi');

Route::get('/check-password', [QueueController::class, 'checkPasswordPage'])->name('password.check');
Route::post('/verify-password', [QueueController::class, 'verifyPassword'])->name('password.verify');

Route::get('/queues/{clinicId}', [QueueController::class, 'index'])->name('queues.index');
Route::post('/queues/{clinicId}/next/{queueNumber}', [QueueController::class, 'next'])->name('queues.next');
Route::post('/queues/{clinicId}/previous/{queueNumber}', [QueueController::class, 'previous'])->name('queues.previous');
Route::post('/queues/{clinicId}/reset/{queueNumber}', [QueueController::class, 'reset'])->name('queues.reset');

