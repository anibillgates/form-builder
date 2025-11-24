<?php

// routes/web.php

use App\Http\Controllers\Admin\FormBuilderController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Form Submissions (User)
    Route::get('/forms', [FormSubmissionController::class, 'index'])->name('forms.index');
    Route::get('/forms/{form}', [FormSubmissionController::class, 'show'])->name('forms.show');
    Route::post('/forms/{form}', [FormSubmissionController::class, 'store'])->name('forms.submit');
    Route::get('/my-submissions', [FormSubmissionController::class, 'mySubmissions'])->name('submissions.my');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Role Management
    Route::resource('roles', RoleController::class);

    // User Management
    Route::resource('users', UserController::class);

    // Form Builder
    Route::resource('forms', FormBuilderController::class);
    
    Route::post('/forms/{form}/toggle-status', [FormBuilderController::class, 'toggleStatus'])
        ->name('forms.toggle-status');

    // Submissions Management
    Route::get('/submissions', [FormSubmissionController::class, 'adminIndex'])
        ->name('submissions.index');
    Route::get('/submissions/{submission}', [FormSubmissionController::class, 'adminShow'])
        ->name('submissions.show');
});

require __DIR__.'/auth.php';