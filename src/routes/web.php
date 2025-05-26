<?php

use App\Http\Controllers\FileManipulatorController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FileManipulatorController::class, 'index'])->name('file-manipulator.index');
Route::post('/process', [FileManipulatorController::class, 'process'])->name('file-manipulator.process');
Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
