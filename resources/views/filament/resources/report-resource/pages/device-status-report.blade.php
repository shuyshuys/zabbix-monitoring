<x-filament::page>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">Laporan Status Perangkat (Up/Down)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-2 py-1">Gedung</th>
                        <th class="px-2 py-1">Lantai</th>
                        <th class="px-2 py-1">Nama Perangkat</th>
                        <th class="px-2 py-1">IP</th>
                        <th class="px-2 py-1">Status</th>
                        <th class="px-2 py-1">Waktu Terakhir Down</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->devices as $device)
                        <tr>
                            <td class="px-2 py-1">{{ $device['gedung'] }}</td>
                            <td class="px-2 py-1">{{ $device['lantai'] }}</td>
                            <td class="px-2 py-1">{{ $device['nama'] }}</td>
                            <td class="px-2 py-1">{{ $device['ip'] }}</td>
                            <td class="px-2 py-1">
                                <span @class([
                                    'text-green-600 font-bold' => $device['status'] === 'Up',
                                    'text-red-600 font-bold' => $device['status'] === 'Down',
                                ])>
                                    {{ $device['status'] }}
                                </span>
                            </td>
                            {{-- <td class="px-2 py-1">{{ $device['available'] }}</td> --}}
                            <td class="px-2 py-1">{{ $device['last_down'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::card>
</x-filament::page>
