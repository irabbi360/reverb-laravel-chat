<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Chat
    Route::get('/chat/{friend}', [ChatController::class, 'chat'])->name('chat.chat');
    Route::get('/messages/{friend}', [ChatController::class, 'messages']);
    Route::post('/messages/{friend}', [ChatController::class, 'sendMessages']);

    Route::get('/messenger', [ChatController::class, 'messenger']);
    Route::get('/messages/{user}', [ChatController::class, 'getUserMessages']);
});

require __DIR__.'/auth.php';
