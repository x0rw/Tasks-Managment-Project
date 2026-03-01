<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('projects.index')
        : redirect()->route('login');
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

// ─── Authenticated routes ─────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // READ-ONLY: accessible to all authenticated users
    Route::get('projects',               [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project}',     [ProjectController::class, 'show'])->name('projects.show');
    Route::get('tasks/{task}',           [TaskController::class,   'show'])->name('tasks.show');

    // Status update: any authenticated user, but controller checks assigned_user_id
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');

    // Comments: open to all authenticated users (no route guard needed)

    // ─── Admin / Manager only ───────────────────────────────────────────────

    Route::middleware('role:admin,manager')->group(function () {

        // Projects — mutating
        Route::get('projects/create',            [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects',                  [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}/edit',    [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}',         [ProjectController::class, 'update'])->name('projects.update');
        Route::patch('projects/{project}',       [ProjectController::class, 'update']);
        Route::delete('projects/{project}',      [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Tasks — mutating (standalone resource)
        Route::get('tasks',                      [TaskController::class, 'index'])->name('tasks.index');
        Route::get('tasks/create',               [TaskController::class, 'create'])->name('tasks.create');
        Route::post('tasks',                     [TaskController::class, 'store'])->name('tasks.store');
        Route::get('tasks/{task}/edit',          [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('tasks/{task}',               [TaskController::class, 'update'])->name('tasks.update');
        Route::patch('tasks/{task}',             [TaskController::class, 'update']);
        Route::delete('tasks/{task}',            [TaskController::class, 'destroy'])->name('tasks.destroy');

        // Task assignment
        Route::patch('/tasks/{task}/assign', [TaskController::class, 'updateAssignment'])
            ->name('tasks.updateAssignment');

        // Tasks nested under a project
        Route::prefix('projects/{project_id}')->group(function () {
            Route::get('/tasks',        [TaskController::class, 'index'])->name('projects.tasks.index');
            Route::get('/tasks/create', [TaskController::class, 'create'])->name('projects.tasks.create');
            Route::post('/tasks/store', [TaskController::class, 'store'])->name('projects.tasks.store');
        });
    });
});
