<table class="min-w-full text-sm">
    <thead>
        <tr>
            <th>Gedung</th>
            <th>Lantai</th>
            <th>Nama Perangkat</th>
            <th>IP</th>
            <th>Status</th>
            <th>Waktu Terakhir Down</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($devices as $device)
            <tr>
                <td>{{ $device['gedung'] }}</td>
                <td>{{ $device['lantai'] }}</td>
                <td>{{ $device['nama'] }}</td>
                <td>{{ $device['ip'] }}</td>
                <td>
                    <span @class([
                        'px-2 py-1 rounded text-xs font-bold',
                        'bg-green-200 text-green-800' => $device['status'] === 'Up',
                        'bg-red-200 text-red-800' => $device['status'] === 'Down',
                    ])>
                        {{ $device['status'] }}
                    </span>
                </td>
                <td>{{ $device['last_down'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
