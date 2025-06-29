<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>CPU Trend Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
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
    <h2>CPU Trend Report - {{ ucfirst($filter) }}</h2>
    <hr>
    <p>Range: {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}</p>
    <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    <hr>
    <p>Min CPU Usage: <b>{{ number_format($trendData['minValue'] ?? 0, 2) }}%</b></p>
    <p>Max CPU Usage: <b>{{ number_format($trendData['maxValue'] ?? 0, 2) }}%</b></p>
    <p>Average CPU Usage: <b>{{ number_format(($trendData['minValue'] + $trendData['maxValue']) / 2, 2) }}%</b></p>
    <hr>
    <h3>CPU Usage Data</h3>

    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>CPU (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $i => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $data[$i] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
