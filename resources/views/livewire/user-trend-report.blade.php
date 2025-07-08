{{-- filepath: resources/views/livewire/user-trend-report.blade.php --}}
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
            <div>Max Lease Count: <b>{{ number_format($trendData['max'] ?? 0) }}</b></div>
            <div>Min Lease Count: <b>{{ number_format($trendData['min'] ?? 0) }}</b></div>
        </div>
        <canvas id="dhcpLeaseChart-{{ $trendData['itemId'] }}" height="80"></canvas>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('dhcpLeaseChart-{{ $trendData['itemId'] }}').getContext('2d');
                if (window['dhcpLeaseChartInstance_{{ $trendData['itemId'] }}']) {
                    window['dhcpLeaseChartInstance_{{ $trendData['itemId'] }}'].destroy();
                }
                window['dhcpLeaseChartInstance_{{ $trendData['itemId'] }}'] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'DHCP Lease Count',
                            data: @json($values),
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
                                title: {
                                    display: true,
                                    text: 'Lease'
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
