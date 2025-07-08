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

    {{-- Statistik Traffic --}}
    <div>
        <h3 class="text-sm font-semibold mb-3 text-gray-800 dark:text-gray-100">Statistik Traffic</h3>
        <div class="flex flex-wrap gap-4">
            <!-- Inbound -->
            <div
                class="flex-1 min-w-[260px] bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                <h4 class="text-green-600 dark:text-green-400 font-semibold mb-2">Inbound</h4>
                <p class="text-sm mb-1"><b>Minimum:</b> {{ number_format($trendData['minValueIn'] ?? 0, 2) }} Mbps</p>
                <p class="text-sm mb-1"><b>Maksimum:</b> {{ number_format($trendData['maxValueIn'] ?? 0, 2) }} Mbps</p>
                <p class="text-sm"><b>Rata-rata:</b> {{ number_format($trendData['avgValueIn'] ?? 0, 2) }} Mbps</p>
            </div>

            <!-- Outbound -->
            <div
                class="flex-1 min-w-[260px] bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                <h4 class="text-blue-600 dark:text-blue-400 font-semibold mb-2">Outbound</h4>
                <p class="text-sm mb-1"><b>Minimum:</b> {{ number_format($trendData['minValueOut'] ?? 0, 2) }} Mbps</p>
                <p class="text-sm mb-1"><b>Maksimum:</b> {{ number_format($trendData['maxValueOut'] ?? 0, 2) }} Mbps</p>
                <p class="text-sm"><b>Rata-rata:</b> {{ number_format($trendData['avgValueOut'] ?? 0, 2) }} Mbps</p>
            </div>
        </div>
    </div>

    <canvas id="trafficChart-{{ $trendData['itemIdIn'] }}" height="80"></canvas>
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
                            label: 'Inbound (mbps)',
                            data: @json($dataIn),
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            tension: 0.5,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Outbound (mbps)',
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
                                text: 'Traffic (mbps)'
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
