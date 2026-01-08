<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskItemController;
use App\Http\Controllers\DelegationController;
use App\Http\Controllers\ProgressUpdateController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin routes (Superuser only)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
    });
    
    // Rooms routes
    Route::resource('rooms', RoomController::class);
    
    // Tasks routes
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/duplicate', [TaskController::class, 'duplicate'])->name('tasks.duplicate');
    
    // Task Items routes (nested under tasks)
    Route::post('/tasks/{task}/task-items', [TaskItemController::class, 'store'])->name('task-items.store');
    Route::get('/tasks/{task}/task-items/{taskItem}/edit', [TaskItemController::class, 'edit'])->name('task-items.edit');
    Route::patch('/tasks/{task}/task-items/{taskItem}', [TaskItemController::class, 'update'])->name('task-items.update');
    Route::post('/tasks/{task}/task-items/{taskItem}/progress', [TaskItemController::class, 'updateProgress'])->name('task-items.update-progress');
    Route::delete('/tasks/{task}/task-items/{taskItem}', [TaskItemController::class, 'destroy'])->name('task-items.destroy');
    
    // Edit progress update (Administrator only)
    Route::get('/tasks/{task}/task-items/{taskItem}/updates/{update}/edit', [TaskItemController::class, 'editUpdate'])->name('task-items.update.edit');
    Route::patch('/tasks/{task}/task-items/{taskItem}/updates/{update}', [TaskItemController::class, 'updateUpdate'])->name('task-items.update.update');
    
    // Delegations routes
    Route::get('/delegations', [DelegationController::class, 'index'])->name('delegations.index');
    Route::get('/tasks/{task}/delegations/create', [DelegationController::class, 'create'])->name('delegations.create');
    Route::post('/tasks/{task}/delegations', [DelegationController::class, 'store'])->name('delegations.store');
    Route::get('/delegations/{delegation}', [DelegationController::class, 'show'])->name('delegations.show');
    Route::get('/delegations/{delegation}/edit', [DelegationController::class, 'edit'])->name('delegations.edit');
    Route::patch('/delegations/{delegation}', [DelegationController::class, 'update'])->name('delegations.update');
    Route::delete('/delegations/{delegation}', [DelegationController::class, 'destroy'])->name('delegations.destroy');
    
    // Progress updates routes
    Route::post('/delegations/{delegation}/progress', [ProgressUpdateController::class, 'store'])->name('progress.store');
    Route::delete('/progress/{progressUpdate}', [ProgressUpdateController::class, 'destroy'])->name('progress.destroy');
    
    // Reports routes
    Route::get('/reports/timeline', [ReportController::class, 'timeline'])->name('reports.timeline');
    Route::get('/reports/tasks/{task}/print', [ReportController::class, 'printTask'])->name('reports.print-task');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
