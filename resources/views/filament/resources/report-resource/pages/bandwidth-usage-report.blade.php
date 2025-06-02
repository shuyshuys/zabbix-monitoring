<x-filament::page>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">Laporan Penggunaan Bandwidth</h2>
        @if (empty($bandwidthData))
            <div class="text-gray-500">Tidak ada data bandwidth.</div>
        @else
            @foreach ($bandwidthData as $device)
                <div class="mb-8">
                    <h3 class="font-semibold mb-2">{{ $device['device'] }}</h3>
                    @foreach ($device['interfaces'] as $iface)
                        <div class="mb-4">
                            <div class="font-medium">{{ $iface['name'] }} ({{ $iface['key'] }})</div>
                            <canvas id="chart-{{ md5($device['device'] . $iface['key']) }}" height="80"></canvas>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const ctx = document.getElementById('chart-{{ md5($device['device'] . $iface['key']) }}').getContext(
                                        '2d');
                                    new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: {!! json_encode(array_map(fn($h) => date('H:i', $h['clock']), $iface['history'])) !!},
                                            datasets: [{
                                                label: 'Bandwidth (bps)',
                                                data: {!! json_encode(array_map(fn($h) => (int) $h['value'], $iface['history'])) !!},
                                                borderColor: '#3b82f6',
                                                backgroundColor: 'rgba(59,130,246,0.1)',
                                                tension: 0.1,
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    title: {
                                                        display: true,
                                                        text: 'bps'
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </x-filament::card>
</x-filament::page>
