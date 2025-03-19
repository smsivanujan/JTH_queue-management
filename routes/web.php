<?php

use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QueueController::class, 'index'])->name('queues.index');
Route::post('/next', [QueueController::class, 'next']);
Route::post('/previous', [QueueController::class, 'previous']);
Route::post('/reset', [QueueController::class, 'reset']);