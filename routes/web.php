<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdminRequestController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCreateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Route::get('/admin/{admin}/logs', [AdminController::class, 'logs'])->name('admin.logs');

Route::get('/check-auth', function() {
    return [
        'check' => Auth::check(),
        'user' => Auth::user(),
        'session' => session()->all(),
    ];
});

Route::get('/', [DashboardController::class, 'showDashboard']);

Route::get('/admin/register', function () {
    $adminExists = \App\Models\User::where('role', 'admin')->exists();
    if ($adminExists) {
        abort(403, 'Admin registration is closed.');
    }
    return view('auth.admin-register');
})->middleware('guest');

Route::post('/admin/register', [RegisteredUserController::class, 'storeAdmin'])
    ->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('created-admins', [App\Http\Controllers\AdminCreateController::class, 'indexCreatedAdmins'])
         ->name('admin.created-admins');
         
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('/history', function () { return view('history'); })->name('history');

    Route::get('/my-requests', [RequestController::class, 'userRequest'])->name('user.requests');
    Route::get('/requests', [RequestController::class, 'index'])->name('requests');
    Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');
    Route::post('/requests/store', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/request-details/{id}', [RequestController::class, 'show'])->name('request-details.show');
    Route::post('/requests/{id}/complete', [RequestController::class, 'complete'])->name('requests.complete');
    Route::post('/requests/{id}/cancel', [RequestController::class, 'cancel'])->name('requests.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware([AdminMiddleware::class, 'auth'])->prefix('admin')->group(function () {
    Route::get('create-admin', [AdminCreateController::class, 'create'])->name('admin.create');
    Route::post('create-admin', [AdminCreateController::class, 'store'])->name('admin.create.store');

    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('requests', [AdminRequestController::class, 'index'])->name('admin.requests');
    Route::get('requests/{id}', [AdminRequestController::class, 'show'])->name('admin.request-details');

    Route::post('requests/accept/{id}', [AdminRequestController::class, 'accept'])->name('admin.requests.accept');
    Route::post('requests/decline/{id}', [AdminRequestController::class, 'decline'])->name('admin.requests.decline');
    Route::post('requests/complete/{id}', [AdminRequestController::class, 'complete'])->name('admin.requests.complete');
});

Route::middleware([AdminMiddleware::class, 'auth'])->prefix('admin')->group(function () {
    Route::get('users', [AdminController::class, 'listUsers'])->name('admin.users');
    Route::get('users/{user}/logs', [UserController::class, 'logs'])
    ->name('admin.users.logs');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('user.requests');
});


require __DIR__.'/auth.php';

