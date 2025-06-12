<?php

namespace App\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Webhook Received', $request->all());

        // Simpan payload webhook ke database
        Log::info('Received webhook', [
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);
        WebhookCall::create([
            'name' => 'zabbix-webhook',
            'url' => $request->fullUrl(),
            'headers' => json_encode($request->headers->all()),
            'payload' => $request->all(),
        ]);

        // Lakukan pemrosesan sesuai kebutuhan
        // Misalnya, jalankan job atau proses lainnya

        $recipients = \App\Models\User::all();
        if (!$recipients) {
            Log::warning('No authenticated user found for notification');
            return response()->json(['status' => 'error', 'message' => 'No authenticated user found'], 403);
        }

        $eventSource = $request['event_source'] ?? 'Unknown Source';
        $eventValue = $request['event_value'] ?? 'No Value';
        $alertSubject = $request['alert_subject'] ?? 'No Subject';
        $alertMessage = $request['alert_message'] ?? 'No Message';

        foreach ($recipients as $recipient) {
            Notification::make()
                ->title("$alertSubject")
                ->body("Alert Message: {$alertMessage}, Source: {$eventSource}, Value: {$eventValue}, Request: " . json_encode($request->all()))
                ->sendToDatabase($recipient); // <-- ini WAJIB
            Log::info('Notification sent to user', ['user_id' => $recipient->id]);
        }

        return response()->json(['status' => 'success']);
    }
}