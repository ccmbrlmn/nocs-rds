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
use App\Http\Controllers\DateNoteController;
use App\Http\Controllers\AssetController;
use App\Models\UserLog;

Route::middleware(['auth','isAdmin'])->group(function () {
    Route::get('/admin/{id}/logs', [AdminController::class, 'logs'])->name('admin.logs');
});

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

Route::get('/date-notes', [DateNoteController::class, 'index'])->middleware('auth');

Route::post('/date-notes', [DateNoteController::class, 'store'])
    ->middleware('auth');
    
    
Route::post('/admin/register', [RegisteredUserController::class, 'storeFirstAdmin'])
    ->middleware('guest');
    
Route::middleware(['auth', 'first.admin'])->group(function () {
    Route::get('created-admins', [AdminCreateController::class, 'indexCreatedAdmins'])
        ->name('admin.created-admins');
});

Route::middleware(['auth', 'verified'])->group(function () {
         
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('/history', function () { return view('history'); })->name('history');

Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('user.requests');
    Route::get('/requests', [RequestController::class, 'index'])->name('requests');
    Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');
    Route::post('/requests/store', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/request-details/{id}', [RequestController::class, 'show'])->name('request-details.show');
    Route::post('/requests/{id}/complete', [RequestController::class, 'complete'])->name('requests.complete');
    Route::post('/requests/{id}/cancel', [RequestController::class, 'cancel'])->name('requests.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/my-requests/pdf', [RequestController::class, 'exportUserPdf'])->name('user.requests.pdf');
    Route::get('/my-requests/csv', [RequestController::class, 'exportUserCsv'])->name('user.requests.csv');
});

Route::middleware(['first.admin'])->group(function () {

    Route::get('create-admin', [AdminCreateController::class, 'create'])
        ->name('admin.create');

    Route::post('create-admin', [AdminCreateController::class, 'store'])
        ->name('admin.create.store');

    Route::get('{id}/edit', [AdminCreateController::class, 'edit'])
        ->name('admin.edit');

    Route::put('{id}/update', [AdminCreateController::class, 'update'])
        ->name('admin.update');

    Route::delete('{id}', [AdminCreateController::class, 'destroy'])
        ->name('admin.destroy');
});

Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->group(function () {

        Route::post('assets', [AssetController::class, 'store'])
            ->name('assets.store');

        Route::put('assets/{id}', [AssetController::class, 'update'])
            ->name('admin.assets.update');
            
        Route::get('assets', [AssetController::class, 'index'])
        ->name('admin.assets');
        
        Route::get('assets/{id}', [AssetController::class, 'show'])
        ->name('admin.assets.show');
        
        Route::get('assets/filter', [AssetController::class, 'index'])
        ->name('admin.assets.filter');
    
        Route::get('assets/pdf', [AssetController::class, 'exportPdf'])
            ->name('admin.assets.pdf');

        Route::get('assets/csv', [AssetController::class, 'exportCsv'])
            ->name('admin.assets.csv');
            
        Route::get('requests/{id}/assign-assets', [AdminRequestController::class, 'assignAssetsPage'])
            ->name('admin.requests.assign');

        Route::post('requests/{id}/assign-assets', [AdminRequestController::class, 'storeAssignedAssets'])
            ->name('admin.requests.assign.store');
            
        Route::post('assets/{id}/retrieve', [AssetController::class, 'retrieved'])
    ->name('assets.retrieve');
    
    });

Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->group(function () {
    
    Route::post('users/{user}/approve', [UserController::class, 'approve'])
    ->name('admin.users.approve');

    Route::get('dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('requests', [AdminRequestController::class, 'index'])
        ->name('admin.requests');
        
    Route::get('requests/pdf', [AdminRequestController::class, 'exportPdf'])
        ->name('admin.requests.pdf');

    Route::get('requests/csv', [AdminRequestController::class, 'exportCsv'])
        ->name('admin.requests.csv');

    Route::get('requests/{id}', [AdminRequestController::class, 'show'])
        ->name('admin.request-details');

    Route::post('requests/accept/{id}', [AdminRequestController::class, 'accept'])
        ->name('admin.requests.accept');

    Route::post('requests/{id}/cancel', [AdminRequestController::class, 'cancel'])
        ->name('admin.requests.cancel');

    Route::post('requests/complete/{id}', [AdminRequestController::class, 'complete'])
        ->name('admin.requests.complete');
        
    Route::post('/return/{id}/accept', [AdminRequestController::class, 'acceptReturn'])
    ->name('return.accept');

    Route::post('/return/{id}/cancel', [AdminRequestController::class, 'cancelReturn'])
        ->name('return.cancel');


    Route::get('users', [UserController::class, 'index'])
    ->name('admin.users');

    Route::get('users/{user}/logs', [UserController::class, 'logs'])
        ->name('admin.users.logs');
        
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->name('admin.users.edit');

    Route::put('users/{user}', [UserController::class, 'update'])
        ->name('admin.users.update');

    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy');
    
    Route::get('notifications', [RequestController::class, 'getAdminNotifications']);
    Route::post('notifications/read', [RequestController::class, 'markNotificationsRead']);

    Route::get('created-admins/pdf', [AdminCreateController::class, 'exportPdf'])
        ->name('admin.created-admins.pdf');

    Route::get('created-admins/csv', [AdminCreateController::class, 'exportCsv'])
        ->name('admin.created-admins.csv');
        
    Route::get('users/pdf', [UserController::class, 'exportPdf'])
        ->name('admin.users.pdf');

    Route::get('users/csv', [UserController::class, 'exportCsv'])
        ->name('admin.users.csv');

    Route::post('/admin/{id}/restore', [AdminCreateController::class, 'restore'])
        ->name('admin.restore');

    Route::post('/admin/users/{id}/restore', [UserController::class, 'restore'])
        ->name('admin.users.restore');
     
});

Route::middleware(['auth'])->group(function () {

    Route::post('/request/{id}/return', [RequestController::class, 'requestReturn'])
        ->name('requests.return');
    
    Route::get('/requests/{id}/edit', [RequestController::class, 'edit'])
    ->name('requests.edit');
    
    Route::post('return/{id}/cancel', [AdminRequestController::class, 'cancelReturn'])
    ->name('admin.return.cancel');

});

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->group(function () {
    // Approve user deletion
    Route::post('users/{user}/approve-deletion', [AdminController::class, 'approveDeletion'])
        ->name('admin.users.approve-deletion');

    // Decline user deletion
    Route::post('users/{user}/decline-deletion', [AdminController::class, 'declineDeletion'])
        ->name('admin.users.decline-deletion');
        
        Route::post('return/{id}/retrieved', [AdminRequestController::class, 'markRetrieved'])
    ->name('admin.return.retrieved');
});

Route::post('/notifications/read', function () {
    $user = Auth::user();
    if (!$user) return response()->json([]);

    UserLog::where('user_id', $user->id)
        ->whereIn('action', ['request_accepted', 'request_cancelled'])
        ->update(['is_read' => true]);

    return response()->json(['status' => 'ok']);
});

Route::middleware(['auth','admin'])->group(function () {
    Route::get('/admin/user-deletion-notifications', [AdminController::class, 'getAdminUserDeletionNotifications']);
    Route::post('/admin/users/{user}/approve-deletion', [AdminController::class, 'approveDeletion']);
    Route::post('/admin/users/{user}/decline-deletion', [AdminController::class, 'declineDeletion']);
});

Route::middleware(['auth'])->group(function () {
    // User notifications
    Route::get('/notifications', [RequestController::class, 'getUserNotifications']);
    

    Route::post('/notifications/read', [RequestController::class, 'markNotificationsRead']);
});

require __DIR__.'/auth.php';

