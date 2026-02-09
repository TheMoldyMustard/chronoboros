<?php
// web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Models\Task; 

Route::get('/', function () {
    $tasks = Task::all();  // Get all tasks from database
    return view('home', ['tasks' => $tasks]);  // ✅ Pass to view
});

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::post('/subjects', [TaskController::class, 'storeSubject'])->name('subjects.store');