<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Memory Trend Report</title>
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
    <h2>Memory Trend Report - {{ ucfirst($filter) }}</h2>
    <hr>
    <p>Range: {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}</p>
    <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    <hr>
    <p>Min Memory Usage: <b>{{ number_format($trendData['minUsed'] ?? 0, 2) }}%</b></p>
    <p>Max Memory Usage: <b>{{ number_format($trendData['maxUsed'] ?? 0, 2) }}%</b></p>
    <p>Average Memory Usage: <b>{{ number_format(($trendData['minUsed'] + $trendData['maxUsed']) / 2, 2) }}%</b></p>
    <hr>
    <h3>Memory Usage Data</h3>

    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Memory (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $i => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $usedMemoryData[$i] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
