<?php

namespace App\Filament\Resources\GKBLT1Resource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

class TracerouteWidget extends Widget
{
    protected static string $view = 'filament.resources.g-k-b-l-t1-resource.widgets.traceroute-widget';
    // protected static string $view = 'filament.widgets.traceroute-widget';

    public ?string $result = null;
    protected int | string | array $columnSpan = 'full';

    public function runTraceroute()
    {
        $target = '192.166.1.254'; // Ganti dengan IP tujuan traceroute Anda
        Log::info('Traceroute target:', ['target' => $target]);
        $output = [];
        $result = null;
        exec("traceroute " . escapeshellarg($target), $output, $result);
        // Log::info('Traceroute command executed', [
        //     'target' => $target,
        //     'result' => $result,
        //     'output' => $output,
        // ]);
        $this->result = implode("\n", $output);
    }
}
