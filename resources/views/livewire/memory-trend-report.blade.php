{{-- filepath: resources/views/livewire/memory-trend-report.blade.php --}}
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
            <div>Min Used Memory: <b>{{ number_format($trendData['minUsed'] ?? 0, 2) }} MB</b></div>
            <div>Max Used Memory: <b>{{ number_format($trendData['maxUsed'] ?? 0, 2) }} MB</b></div>
            {{-- <pre>@json($this)</pre> --}}
        </div>
        <canvas id="memoryChart-{{ $trendData['itemIdUsed'] }}" height="80"></canvas>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('memoryChart-{{ $trendData['itemIdUsed'] }}').getContext('2d');
                if (window['memoryChartInstance_{{ $trendData['itemIdUsed'] }}']) {
                    window['memoryChartInstance_{{ $trendData['itemIdUsed'] }}'].destroy();
                }
                console.log('Rendering Memory Chart');
                console.log('Labels:', @json($labels));
                console.log('Data:', @json($usedMemoryData)); // Updated to use the correct variable
                window['memoryChartInstance_{{ $trendData['itemIdUsed'] }}'] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Used Memory (MB)',
                            data: @json($usedMemoryData),
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            tension: 0.5
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
                                    text: 'MB'
                                }
                            },
                            x: {
                                ticks: {
                                    autoSkip: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
</div>
