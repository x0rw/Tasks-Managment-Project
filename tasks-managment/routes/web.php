<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

Route::resource('tasks', TaskController::class);

// TO update the status alone from the table
Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
    ->name(name: 'tasks.updateStatus');

// to only assign one user
Route::patch('/tasks/{task}/assign', [TaskController::class, 'updateAssignment'])
    ->name('tasks.updateAssignment');

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('projects/{project_id}')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('projects.tasks.index');
    // Route::get('/tasks/{task_id}', [TaskController::class, 'show'])->name('projects.tasks.show');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('projects.tasks.create');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('projects.tasks.store');
});


Route::resource('projects', ProjectController::class);
