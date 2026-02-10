<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/archived', [TaskController::class, 'archived'])->name('tasks.archived');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::get('/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
Route::post('/tasks/{id}/archive', [TaskController::class, 'archive'])->name('tasks.archive');
Route::post('/tasks/{id}/unarchive', [TaskController::class, 'unarchive'])->name('tasks.unarchive');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::post('/subjects', [TaskController::class, 'storeSubject'])->name('subjects.store');