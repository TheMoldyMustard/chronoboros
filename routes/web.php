<?php

use Illuminate\Support\Facades\Route;
use App\Models\Task; 

Route::get('/', function () {
    $tasks = Task::all();  // Get all tasks from database
    return view('home', ['tasks' => $tasks]);  // ✅ Pass to view
});
