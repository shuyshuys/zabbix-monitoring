<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return redirect()->route('filament.monitoring.auth.login');
});

Route::post('/webhooks/receive', [WebhookController::class, 'handle']);
