{{-- filepath: resources/views/livewire/icmp-ping-trend-report.blade.php --}}
<div class="space-y-4">
    <div class="flex items-center gap-4">
        <select wire:model="filter"
            class="border rounded px-3 py-2 text-sm bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
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
            <div>Max Response Time: <b>{{ $trendData['maxResponse'] ?? '-' }} ms</b></div>
            <div>Min Response Time: <b>{{ $trendData['minResponse'] ?? '-' }} ms</b></div>
        </div>
        <canvas id="icmpPingChart-{{ $trendData['itemIdStatus'] }}" height="80"></canvas>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('icmpPingChart-{{ $trendData['itemIdStatus'] }}').getContext('2d');
                if (window['icmpPingChartInstance_{{ $trendData['itemIdStatus'] }}']) {
                    window['icmpPingChartInstance_{{ $trendData['itemIdStatus'] }}'].destroy();
                }
                window['icmpPingChartInstance_{{ $trendData['itemIdStatus'] }}'] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                                label: 'ICMP Status',
                                data: @json($statusData),
                                borderColor: '#4CAF50',
                                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                                tension: 0.3,
                                yAxisID: 'y',
                            },
                            {
                                label: 'ICMP Response Time (ms)',
                                data: @json($responseTimeData),
                                borderColor: '#2196F3',
                                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                                tension: 0.3,
                                yAxisID: 'y1',
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
                                    text: 'Status'
                                },
                                position: 'left',
                                min: 0,
                                max: 1
                            },
                            y1: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Response Time (ms)'
                                },
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
