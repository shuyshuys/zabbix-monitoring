{{-- filepath: resources/views/livewire/dhcp-lease-trend-report.blade.php --}}
<div>
    <form wire:submit.prevent="downloadPdf" class="flex items-center gap-4 mb-4">
        <select wire:model="filter" class="border rounded px-2 py-1">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
        </select>
        <x-filament::button color="primary" type="submit" class="mb-0">
            Download PDF
        </x-filament::button>
    </form>

    <div class="mb-4">
        <div>Range: {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}</div>
        <div>Total DHCP Lease (avg): <b>{{ number_format($trendData['total'] ?? 0) }}</b></div>
    </div>

    <div>
        <canvas id="dhcpLeaseChart" class="w-full max-w-2xl h-64 bg-white rounded shadow"></canvas>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let dhcpLeaseChartInstance = null;
        document.addEventListener('livewire:load', () => {
            const ctx = document.getElementById('dhcpLeaseChart').getContext('2d');

            function renderChart(labels, data) {
                if (dhcpLeaseChartInstance) dhcpLeaseChartInstance.destroy();
                dhcpLeaseChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'DHCP Lease Count',
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
                            }
                        }
                    }
                });
            }

            renderChart(@json($labels), @json($values));
            Livewire.hook('message.processed', (message, component) => {
                renderChart(@json($labels), @json($values));
            });
        });
    </script>
@endpush
