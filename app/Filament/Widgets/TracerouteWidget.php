<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

class TracerouteWidget extends Widget
{
    protected static string $view = 'filament.widgets.traceroute-widget';

    public ?string $target = null;
    public ?string $result = null;
    protected int | string | array $columnSpan = 'full';

    public function runTraceroute()
    {
        // Log::info('Traceroute target:', ['target' => $this->target]);
        // if (!$this->target) {
        //     $this->result = 'Target tidak dipilih.';
        //     Log::warning('Target tidak dipilih');
        //     return;
        // }
        // $output = [];
        // $result = null;
        // exec("traceroute " . escapeshellarg($this->target), $output, $result);
        // Log::info('Traceroute command executed', [
        //     'target' => $this->target,
        //     'result' => $result,
        //     'output' => $output,
        // ]);
        // Log::info('Traceroute output:', ['output' => $output, 'result' => $result]);
        // $this->result = implode("\n", $output);
    }
}