<x-filament::card>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="space-y-6">
        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Perbandingan Metrik antar Gedung</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300">Bandingkan berbagai metrik untuk analisis yang lebih baik.
        </p>

        {{-- Filter Section --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Pilih Gedung (Checkbox) --}}
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Pilih Gedung</label>
                <div class="border rounded px-3 py-2 dark:bg-gray-800 dark:text-white h-40 overflow-y-auto">
                    @foreach ($availableHosts as $host => $label)
                        <label class="flex items-center space-x-2 mb-1">
                            <input type="checkbox" wire:model="selectedHosts" value="{{ $host }}"
                                class="form-checkbox text-primary-600 dark:text-primary-400">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Pilih Metrik --}}
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Pilih Metrik</label>
                <select wire:model="metric"
                    class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-800 dark:text-white">
                    <option value="cpu">CPU Usage</option>
                    <option value="memory">Memory Usage</option>
                    <option value="dhcp">DHCP User Count</option>
                    <option value="traffic">Traffic Count</option>
                </select>
            </div>

            {{-- Rentang Waktu --}}
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Rentang Waktu</label>
                <select wire:model="filter"
                    class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-800 dark:text-white">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last_week">Last Week</option>
                    <option value="last_month">Last Month</option>
                    <option value="last_3_month">Last 3 Months</option>
                    <option value="last_6_month">Last 6 Months</option>
                    <option value="last_year">Last Year</option>
                </select>
            </div>
        </div>

        {{-- Tombol Submit dan Loading Indicator --}}
        <div class="mt-4 flex items-center gap-4">
            <x-filament::button wire:click="generateChart" wire:loading.attr="disabled">
                Tampilkan Grafik
            </x-filament::button>

            <div wire:loading wire:target="generateChart" class="text-sm text-gray-600 dark:text-gray-300">
                Memuat data grafik...
            </div>
        </div>

        {{-- Chart Section --}}
        <div class="mt-4 w-full" x-data="{
            chartData: @entangle('chartData'),
            chartInstance: null,
            renderChart() {
                const ctx = document.getElementById('compareChart');
                if (!ctx || !this.chartData.length) return;
        
                const labels = this.chartData[0]?.labels ?? [];
                const datasets = this.chartData.map(dataset => ({
                    label: dataset.label,
                    data: dataset.data,
                    fill: false,
                    borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
                    tension: 0.4
                }));
        
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }
        
                this.chartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: { labels, datasets },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Data' }
                            }
                        }
                    }
                });
            }
        }" x-init="renderChart()" x-effect="renderChart()">
            <canvas id="compareChart" height="200" class="w-full block"></canvas>
        </div>
    </div>

</x-filament::card>
<script>
    // window.addEventListener('renderChart', () => {
    //     renderChart(@this.get('chartData'));
    // });

    function renderChart(chartData) {
        const ctx = document.getElementById('compareChart');
        if (!ctx || !chartData.length) return;

        const labels = chartData[0]?.labels ?? [];
        const datasets = chartData.map(dataset => ({
            label: dataset.label,
            data: dataset.data,
            fill: false,
            borderColor: '#' + Math.floor(Math.random() * 16777215).toString(16),
            tension: 0.4
        }));

        if (window.compareChartInstance) {
            window.compareChartInstance.destroy();
        }

        window.compareChartInstance = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels,
                datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Data (%)'
                        }
                    }
                }
            }
        });
    }
</script>
