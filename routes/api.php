<?php

use App\Http\Controllers\ChatController;

Route::post('/send-message', [ChatController::class, 'sendMessage']);
