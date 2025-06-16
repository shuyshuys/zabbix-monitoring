<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

// ...existing code...
class TracerouteWidget extends Widget
{
    protected static string $view = 'filament.widgets.traceroute-widget';

    public ?string $result = null;
    public ?string $target = null;
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function runTraceroute()
    {
        $target = $this->target ?: '8.8.8.8';
        Log::info('Traceroute target:', ['target' => $target]);
        $output = [];
        $result = null;
        exec("traceroute " . escapeshellarg($target), $output, $result);
        $this->result = implode("\n", $output);
        Log::info('Traceroute result:', ['result' => $this->result]);
    }
}
