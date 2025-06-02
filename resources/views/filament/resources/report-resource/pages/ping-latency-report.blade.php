<x-filament::page>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">Laporan Ping & Latency</h2>
        @foreach ($pingData as $device)
            <div class="mb-8">
                <h3 class="font-semibold mb-2">{{ $device['device'] }}</h3>
                <div class="mb-2">
                    <span class="font-medium">Rata-rata Latency:</span>
                    <span class="text-blue-600">{{ $device['avg_latency'] ?? '-' }} s</span>
                    &nbsp;|&nbsp;
                    <span class="font-medium">Latency Tertinggi:</span>
                    <span class="text-red-600">{{ $device['max_latency'] ?? '-' }} s</span>
                </div>
                <canvas id="latency-chart-{{ md5($device['device']) }}" height="60"></canvas>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('latency-chart-{{ md5($device['device']) }}').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: {!! json_encode(array_map(fn($h) => date('H:i', $h['clock']), $device['latency_history'])) !!},
                                datasets: [{
                                    label: 'Latency (s)',
                                    data: {!! json_encode(array_map(fn($h) => (float) $h['value'], $device['latency_history'])) !!},
                                    borderColor: '#f59e42',
                                    backgroundColor: 'rgba(245,158,66,0.1)',
                                    tension: 0.1,
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Seconds'
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </div>
        @endforeach
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </x-filament::card>
</x-filament::page>
