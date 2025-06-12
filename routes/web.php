<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
// use Marjose123\FilamentWebhookServer\Http\Controllers\WebhookController;
// use Tapp\WebhookClient\Http\Controllers\WebhookController;

Route::get('/', function () {
    return view('welcome');
});

Route::webhooks('webhook-receiving-url');

Route::post('/webhooks/receive', [WebhookController::class, 'handle']);