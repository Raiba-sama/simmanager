<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/dashboard/chart-data', [\App\Http\Controllers\DashboardController::class, 'getChartDataApi'])->middleware('auth')->name('dashboard.chart-data');

Route::middleware('auth')->group(function () {
    // Search
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'search'])->name('search');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/all', [\App\Http\Controllers\NotificationController::class, 'all'])->name('notifications.all');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Documents
    Route::get('/documents', [\App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{simRequest}/bordereau', [\App\Http\Controllers\DocumentController::class, 'show'])->name('documents.bordereau');
    
    // Mail Sent
    Route::get('/mail-sent', [\App\Http\Controllers\MailSentController::class, 'index'])->name('mail-sent.index');
    Route::post('/mail-sent/store', [\App\Http\Controllers\MailSentController::class, 'store'])->name('mail-sent.store');
    Route::post('/mail-sent/check-mail', [\App\Http\Controllers\MailSentController::class, 'checkMail'])->name('mail-sent.check-mail');
    Route::post('/mail-sent/{id}/update-status', [\App\Http\Controllers\MailSentController::class, 'updateStatus'])->name('mail-sent.update-status');
    
    // SIM Requests
    Route::get('sim-requests/export', [\App\Http\Controllers\SimRequestController::class, 'export'])->name('sim-requests.export');
    Route::resource('sim-requests', \App\Http\Controllers\SimRequestController::class);
    Route::post('sim-requests/{simRequest}/approve', [\App\Http\Controllers\SimRequestController::class, 'approve'])
        ->name('sim-requests.approve');
    Route::post('sim-requests/{simRequest}/reject', [\App\Http\Controllers\SimRequestController::class, 'reject'])
        ->name('sim-requests.reject');
    Route::post('sim-requests/{simRequest}/admin-action', [\App\Http\Controllers\SimRequestController::class, 'adminAction'])
        ->name('sim-requests.admin-action');
    Route::post('sim-requests/{simRequest}/quick-update-status', [\App\Http\Controllers\SimRequestController::class, 'quickUpdateStatus'])
        ->name('sim-requests.quick-update-status');
    Route::delete('sim-requests/{simRequest}/cancel', [\App\Http\Controllers\SimRequestController::class, 'cancel'])
        ->name('sim-requests.cancel');
    Route::delete('sim-requests/{simRequest}/destroy', [\App\Http\Controllers\SimRequestController::class, 'destroy'])
        ->name('sim-requests.destroy');
    Route::post('sim-requests/{simRequest}/submit-webhook', [\App\Http\Controllers\SimRequestController::class, 'submitToWebhook'])
        ->name('sim-requests.submit-webhook');
    Route::get('sim-requests/{simRequest}/bordereau', [\App\Http\Controllers\SimRequestController::class, 'generateBordereau'])
        ->name('sim-requests.bordereau');
    Route::post('sim-requests/bulk-approve', [\App\Http\Controllers\SimRequestController::class, 'bulkApprove'])
        ->name('sim-requests.bulk-approve');
    Route::post('sim-requests/bulk-reject', [\App\Http\Controllers\SimRequestController::class, 'bulkReject'])
        ->name('sim-requests.bulk-reject');
    Route::post('sim-requests/bulk-update-status', [\App\Http\Controllers\SimRequestController::class, 'bulkUpdateStatus'])
        ->name('sim-requests.bulk-update-status');
    Route::delete('sim-requests/bulk-delete', [\App\Http\Controllers\SimRequestController::class, 'bulkDelete'])
        ->name('sim-requests.bulk-delete');
    Route::post('sim-requests/{simRequest}/toggle-favorite', [\App\Http\Controllers\SimRequestController::class, 'toggleFavorite'])
        ->name('sim-requests.toggle-favorite');

    // SIMs
    Route::get('sims/export', [\App\Http\Controllers\SimController::class, 'export'])->name('sims.export');
    Route::post('sims/{sim}/update-status', [\App\Http\Controllers\SimController::class, 'updateStatus'])->name('sims.update-status');
    Route::resource('sims', \App\Http\Controllers\SimController::class)->except(['create', 'edit', 'destroy']);
    Route::post('sims/{sim}/assign', [\App\Http\Controllers\SimController::class, 'assign'])->name('sims.assign');
    Route::post('sims/{sim}/unassign', [\App\Http\Controllers\SimController::class, 'unassign'])->name('sims.unassign');
    Route::post('sims/bulk-assign', [\App\Http\Controllers\SimController::class, 'bulkAssign'])->name('sims.bulk-assign');
    Route::post('sims/bulk-unassign', [\App\Http\Controllers\SimController::class, 'bulkUnassign'])->name('sims.bulk-unassign');
    Route::post('sims/import-csv', [\App\Http\Controllers\SimController::class, 'importCsv'])->name('sims.import-csv');
});

require __DIR__.'/auth.php';
