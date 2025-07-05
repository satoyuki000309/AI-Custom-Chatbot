<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AISettingsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QnAController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $qnas = \App\Models\QnA::latest()->paginate(10);
    return view('dashboard', compact('qnas'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('/admin/qna', QnAController::class);

    // AI Settings routes
    Route::get('/ai-settings', [AISettingsController::class, 'index'])->name('ai-settings.index');
    Route::post('/ai-settings/model', [AISettingsController::class, 'updateModel'])->name('ai-settings.model');
    Route::post('/ai-settings/upload', [AISettingsController::class, 'uploadFile'])->name('ai-settings.upload');
    Route::delete('/ai-settings/file', [AISettingsController::class, 'deleteFile'])->name('ai-settings.delete-file');
    Route::post('/ai-settings/clear-files', [AISettingsController::class, 'clearAllFiles'])->name('ai-settings.clear-files');

    // Activity Log routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::post('/activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');

    // Custom Messages routes
    Route::resource('/custom-messages', CustomMessageController::class);
    Route::post('/custom-messages/{customMessage}/toggle-status', [CustomMessageController::class, 'toggleStatus'])->name('custom-messages.toggle-status');
    Route::post('/custom-messages/{customMessage}/preview', [CustomMessageController::class, 'preview'])->name('custom-messages.preview');
    Route::post('/custom-messages/test', [CustomMessageController::class, 'test'])->name('custom-messages.test');
});

Route::get('/chat-widget', function () {
    return view('widget');
});

Route::post('/api/send-message', [ChatController::class, 'sendMessage']);
Route::get('/api/welcome-message', [ChatController::class, 'getWelcomeMessage']);

Route::get('/admin/chats/export', [App\Http\Controllers\ChatExportController::class, 'export'])->name('chats.export');

require __DIR__ . '/auth.php';
