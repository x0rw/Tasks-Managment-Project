<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('projects.index')
        : view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    // Projects resource routes
    Route::resource('projects', ProjectController::class);

    // Tags resource routes
    Route::resource('tags', \App\Http\Controllers\TagController::class)->only(['index', 'store', 'update', 'destroy']);

    // All task routes scoped to projects
    Route::prefix('projects/{project}')->name('projects.')->group(function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        
        // Quick update routes for inline editing
        Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
        Route::patch('/tasks/{task}/assign', [TaskController::class, 'updateAssignment'])->name('tasks.updateAssignment');
        Route::patch('/tasks/{task}/priority', [TaskController::class, 'updatePriority'])->name('tasks.updatePriority');
        Route::patch('/tasks/{task}/due-date', [TaskController::class, 'updateDueDate'])->name('tasks.updateDueDate');
        Route::patch('/tasks/{task}/tags', [TaskController::class, 'updateTags'])->name('tasks.updateTags');
    });
});
