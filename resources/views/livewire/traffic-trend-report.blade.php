{{-- filepath: resources/views/livewire/traffic-trend-report.blade.php --}}
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
        {{-- <x-filament::button color="primary" type="submit" class="mb-0">
            Download PDF
        </x-filament::button> --}}
        <x-filament::button color="primary" type="button" wire:click="downloadPdf" class="mb-0">
            Download PDF
        </x-filament::button>
    </div>

    <div>
        <div class="mb-4">
            <div>Range: {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}</div>
            <div>Total Inbound: <b>{{ number_format($trendData['total_in'] ?? 0) }} MB</b></div>
            <div>Total Outbound: <b>{{ number_format($trendData['total_out'] ?? 0) }} MB</b></div>
        </div>

        <canvas id="trafficChart-{{ $trendData['itemIdIn'] }}" height="80"></canvas>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function renderTrafficChart() {
            const ctx = document.getElementById('trafficChart-{{ $trendData['itemIdIn'] }}');
            if (!ctx) return;
            const chartCtx = ctx.getContext('2d');
            if (window['trafficChartInstance_{{ $trendData['itemIdIn'] }}']) {
                window['trafficChartInstance_{{ $trendData['itemIdIn'] }}'].destroy();
            }
            window['trafficChartInstance_{{ $trendData['itemIdIn'] }}'] = new Chart(chartCtx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [{
                            label: 'Inbound (MB)',
                            data: @json($dataIn),
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            tension: 0.5,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Outbound (MB)',
                            data: @json($dataOut),
                            borderColor: '#2196F3',
                            backgroundColor: 'rgba(33, 150, 243, 0.1)',
                            tension: 0.5,
                            yAxisID: 'y',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Traffic (MB)'
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', renderTrafficChart);
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', function() {
                renderTrafficChart();
            });
        });
    </script>
@endpush
