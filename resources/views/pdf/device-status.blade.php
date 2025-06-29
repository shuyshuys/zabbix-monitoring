{{-- filepath: resources/views/exports/device-status-report.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Status Perangkat</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h2>Laporan Status Perangkat (Up/Down)</h2>
    <table>
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
                    <td>{{ $device['status'] }}</td>
                    <td>{{ $device['last_down'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
