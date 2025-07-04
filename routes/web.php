<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QnAController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('/admin/qna', QnAController::class);
});

Route::get('/chat-widget', function () {
    return view('widget');
});

Route::post('/api/send-message', [ChatController::class, 'sendMessage']);

Route::get('/admin/chats/export', [App\Http\Controllers\ChatExportController::class, 'export'])->name('chats.export');

require __DIR__ . '/auth.php';
