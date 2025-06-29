{{-- filepath: resources/views/exports/dhcp-lease-trend-report.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>DHCP Lease Trend Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .data {
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="title">DHCP Lease Trend Report ({{ ucfirst($filter) }})</div>
    <hr>
    <div class="data">Range: {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}</div>
    <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    <hr>
    <div class="data">Max Lease Count: <b>{{ number_format($trendData['max'] ?? 0) }}</b></div>
    <div class="data">Min Lease Count: <b>{{ number_format($trendData['min'] ?? 0) }}</b></div>
    <hr>
    <h3>DHCP Lease Data</h3>

    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Lease Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $i => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $values[$i] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
