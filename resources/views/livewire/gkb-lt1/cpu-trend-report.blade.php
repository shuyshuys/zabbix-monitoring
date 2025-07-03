<div class="space-y-4">
    <div class="flex items-center gap-4">
        <select wire:model="filter" class="border rounded px-3 py-2 text-sm">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="last_week">Last Week</option>
            <option value="last_month">Last Month</option>
            <option value="last_3_month">Last 3 Months</option>
            <option value="last_6_month">Last 6 Months</option>
            <option value="last_year">Last Year</option>
        </select>
        {{-- <button wire:click="downloadPdf" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
            Download PDF
        </button> --}}
        {{-- filepath: resources/views/livewire/gkb-lt1/cpu-trend-report.blade.php --}}
        <x-filament::button color="primary" type="button" wire:click="downloadPdf" class="mb-0">
            Download PDF
        </x-filament::button>

    </div>

    <div>
        <canvas id="cpuChart" class="w-full max-w-2xl h-64 bg-white rounded shadow"></canvas>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        let cpuChartInstance = null;
        document.addEventListener('livewire:load', () => {
            const ctx = document.getElementById('cpuChart').getContext('2d');

            function renderChart(labels, data) {
                if (cpuChartInstance) cpuChartInstance.destroy();
                cpuChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'CPU (%)',
                            data: data,
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
                                max: 100
                            }
                        }
                    }
                });
            }

            // Initial render
            renderChart(@json($labels), @json($data));

            // Re-render on Livewire update
            Livewire.hook('message.processed', (message, component) => {
                renderChart(@json($labels), @json($data));
            });
        });
    </script>
@endpush
