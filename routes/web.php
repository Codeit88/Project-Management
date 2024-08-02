<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard2', function () {
    return Inertia::render('Dashboard2');
})->middleware(['auth', 'verified'])->name('dashboard2');


Route::get('/projects', function () {
    return Inertia::render('Projects');
})->middleware(['auth', 'verified'])->name('projects');


Route::get('/tasks', function () {
    return Inertia::render('TaskProject');
})->middleware(['auth', 'verified'])->name('tasks');

Route::get('/projects2', function () {
    return Inertia::render('UserProjects');
})->middleware(['auth', 'verified'])->name('projects2');

Route::get('/users', function () {
    return Inertia::render('Users');
})->middleware(['auth', 'verified'])->name('users');



Route::get('/comments', function () {
    return Inertia::render('Comments');
})->middleware(['auth', 'verified'])->name('comments');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('v1/admin')->middleware('auth')->group(function () {
    // Project Management
    Route::get('project', [ProjectController::class, 'index']);
    Route::post('project', [ProjectController::class, 'store']);
    Route::put('project/{project}', [ProjectController::class, 'update']);
    Route::delete('project/{project}', [ProjectController::class, 'destroy']);
    Route::post('project/{project}/assign', [ProjectController::class, 'assign']);

    // User Management
    Route::get('user', [UserController::class, 'index']);
    Route::post('user', [UserController::class, 'store']);
    Route::put('user/{user}', [UserController::class, 'update']);
    Route::delete('user/{user}', [UserController::class, 'destroy']);
});



Route::prefix('v1')->group(function () {
    Route::prefix('project')->group(function () {
        Route::get('/', [ProjectController::class, 'index2']);
        Route::get('/{project}', [ProjectController::class, 'show2']);
        Route::post('/addTask', [TaskController::class, 'addTask']);
        Route::get('/getTask', [TaskController::class, 'index']);
        Route::post('/assign', [TaskController::class, 'assignTask']);
        
        Route::post('/delete', [TaskController::class, 'delete']);
        Route::prefix('{project}/task')->group(function () {
            
        Route::get('/', [TaskController::class, 'index']);
        Route::put('/{task}', [TaskController::class, 'update']);
            Route::post('/', [TaskController::class, 'store']);
            Route::get('/{task}', [TaskController::class, 'show']);
            Route::delete('/{task}', [TaskController::class, 'destroy']);
            Route::post('/{task}/assign', [TaskController::class, 'assign']);
            
            Route::prefix('{task}/comment')->group(function () {
                Route::get('/', [CommentController::class, 'index']);
                Route::post('/', [CommentController::class, 'store']);
                Route::put('/{comment}', [CommentController::class, 'update']);
                Route::delete('/{comment}', [CommentController::class, 'destroy']);
            });
        });
    });
});




require __DIR__.'/auth.php';
