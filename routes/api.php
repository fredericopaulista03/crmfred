<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Evolution API Webhook (no auth required)
Route::post('/webhook/evolution', [WebhookController::class, 'evolution'])->name('webhook.evolution');
