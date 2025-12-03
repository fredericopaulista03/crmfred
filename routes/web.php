<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WebhookController;
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

    // Kanban Routes
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban/columns', [KanbanController::class, 'storeColumn'])->name('kanban.columns.store');
    Route::post('/kanban/cards', [KanbanController::class, 'storeCard'])->name('kanban.cards.store');
    Route::post('/kanban/cards/reorder', [KanbanController::class, 'updateCardOrder'])->name('kanban.cards.reorder');

    // Chat Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Settings Routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('role:admin');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('role:admin');
    Route::post('/settings/test', [SettingsController::class, 'testConnection'])->name('settings.test')->middleware('role:admin');
});

require __DIR__.'/auth.php';
