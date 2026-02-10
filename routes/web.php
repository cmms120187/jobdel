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
use App\Http\Controllers\Leader\SubordinateController;
use App\Http\Controllers\Leader\ReportController as LeaderReportController;
use App\Http\Controllers\StoController;
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
    // Secure file download for task attachments
    Route::get('/tasks/{task}/file/{fileKey}', [TaskController::class, 'downloadFile'])->name('tasks.download-file');
    Route::get('/tasks/{task}/preview/{fileKey}', [TaskController::class, 'previewFile'])->name('tasks.preview-file');
    Route::post('/tasks/{task}/attachments', [TaskController::class, 'uploadAttachment'])->name('tasks.upload-attachment');
    Route::delete('/tasks/{task}/attachments/{attachment}', [TaskController::class, 'deleteAttachment'])->name('tasks.delete-attachment');
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
    // Secure download for progress update attachments
    Route::get('/progress/{progressUpdate}/file/{index}', [ProgressUpdateController::class, 'downloadAttachment'])->name('progress.download-file');
    
    // STO (Struktur Organisasi berdasarkan hirarki users)
    Route::get('/sto', [StoController::class, 'index'])->name('sto.index');

    // Reports routes
    Route::get('/reports/timeline', [ReportController::class, 'timeline'])->name('reports.timeline');
    Route::get('/reports/tasks/{task}/print', [ReportController::class, 'printTask'])->name('reports.print-task');
    
    // Leader routes: recursive subordinates
    Route::get('/leader/subordinates', [SubordinateController::class, 'index'])->name('leader.subordinates.index');
    Route::get('/leader/subordinates/{id}', [SubordinateController::class, 'show'])->name('leader.subordinates.show');
    // Leader reports
    Route::get('/leader/reports/overview', [LeaderReportController::class, 'overview'])->name('leader.reports.overview');
    Route::get('/leader/reports/tasks', [LeaderReportController::class, 'tasks'])->name('leader.reports.tasks');
    Route::get('/leader/reports/work-time', [LeaderReportController::class, 'workTimeReport'])->name('leader.reports.work-time');
    Route::get('/leader/reports/work-time-history', [LeaderReportController::class, 'workTimeHistory'])->name('leader.reports.work-time-history');
    Route::get('/leader/reports/user-reports', [LeaderReportController::class, 'userReports'])->name('leader.reports.user-reports');
    Route::get('/leader/reports/user/{userId}', [LeaderReportController::class, 'userDetail'])->name('leader.reports.user-detail');
    Route::get('/leader/reports/export', [LeaderReportController::class, 'export'])->name('leader.reports.export');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
