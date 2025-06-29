{{-- filepath: resources/views/livewire/cpu-trend-report.blade.php --}}
<div class="space-y-4">
    <div class="flex items-center gap-4">
        <select wire:model="filter"
            class="border rounded px-3 py-2 text-sm bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="last_week">Last Week</option>
            <option value="last_month">Last Month</option>
            <option value="last_3_month">Last 3 Months</option>
            <option value="last_6_month">Last 6 Months</option>
            <option value="last_year">Last Year</option>
        </select>
        <x-filament::button color="primary" type="button" wire:click="downloadPdf" class="mb-0">
            Download PDF
        </x-filament::button>
    </div>

    <div>
        <div class="mb-4">
            <div>Range: {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}</div>
            <div>Min CPU Usage: <b>{{ number_format($trendData['minValue'] ?? 0, 2) }}%</b></div>
            <div>Max CPU Usage: <b>{{ number_format($trendData['maxValue'] ?? 0, 2) }}%</b></div>
            {{-- <pre>@json($this)</pre> --}}
        </div>
        <canvas id="cpuChart-{{ $this->trendData['itemId'] }}" height="80"></canvas>
        {{-- <pre>@json($this)</pre> --}}
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function renderCpuChart() {
            const ctx = document.getElementById('cpuChart-{{ $trendData['itemId'] }}');
            if (!ctx) return;
            const chartCtx = ctx.getContext('2d');
            if (window['cpuChartInstance_{{ $trendData['itemId'] }}']) {
                window['cpuChartInstance_{{ $trendData['itemId'] }}'].destroy();
            }
            window['cpuChartInstance_{{ $trendData['itemId'] }}'] = new Chart(chartCtx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'CPU (%)',
                        data: @json($data),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: '%'
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', renderCpuChart);
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', function() {
                renderCpuChart();
            });
        });
    </script>
@endpush
