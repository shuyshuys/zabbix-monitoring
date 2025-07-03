<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
// use Marjose123\FilamentWebhookServer\Http\Controllers\WebhookController;
// use Tapp\WebhookClient\Http\Controllers\WebhookController;

Route::get('/', function () {
    return redirect()->route('filament.monitoring.auth.login');
});

Route::webhooks('webhook-receiving-url');

Route::post('/webhooks/receive', [WebhookController::class, 'handle']);

// Route::get('/monitoring/reports/device-status/download', [::class, 'downloadPdf'])
//     ->name('filament.resources.report-resource.pages.device-status-report.download-pdf');