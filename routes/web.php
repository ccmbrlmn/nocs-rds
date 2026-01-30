<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/check-auth', function() {
    return [
        'check' => Auth::check(),
        'user' => Auth::user(),
        'session' => session()->all(),
    ];
});


// Guest-only routes
Route::middleware('guest')->group(function () {

Route::get('/register', function () {
    // Forget old intended URLs so it won't redirect to login
    session()->forget('url.intended');
    return view('auth.register'); // show the registration form
})->name('register');

Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    
});

// Public dashboard (anyone can access)
Route::get('/', [DashboardController::class, 'showDashboard']); // optional home page
Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');


// Authenticated routes (admin/dashboard)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin-dashboard', [DashboardController::class, 'showDashboard'])->name('admin-dashboard');

    Route::get('/history', function () { return view('history'); })->name('history');
    Route::get('/my-requests', [RequestController::class, 'userRequest'])->name('user.requests');
    Route::get('/requests', [RequestController::class, 'index'])->name('requests');
    Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');
    Route::post('/requests/store', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/request-details/{id}', [RequestController::class, 'show'])->name('request-details.show');
    Route::post('/requests/{id}/accept', [RequestController::class, 'accept'])->name('requests.accept');
    Route::post('/requests/{id}/complete', [RequestController::class, 'complete'])->name('requests.complete');
    Route::post('/requests/{id}/decline', [RequestController::class, 'decline'])->name('requests.decline');
    Route::post('/requests/{id}/cancel', [RequestController::class, 'cancel'])->name('requests.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth scaffolding
require __DIR__.'/auth.php';

