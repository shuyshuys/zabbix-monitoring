<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>CPU Trend Report</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #1F2937;
            padding: 2rem;
        }

        h2 {
            font-size: 18px;
            font-weight: 600;
            margin-top: 2rem;
            color: #111827;
        }

        h3 {
            font-size: 14px;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        hr {
            margin: 1rem 0;
            border: none;
            border-top: 1px solid #E5E7EB;
        }

        .summary p {
            margin: 0.25rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }

        th {
            background-color: #F3F4F6;
            font-weight: 600;
        }

        th,
        td {
            border: 1px solid #D1D5DB;
            padding: 6px 10px;
            text-align: center;
        }

        .stat {
            font-weight: 600;
            color: #111827;
        }

        .timestamp {
            font-size: 11px;
            color: #6B7280;
        }
    </style>
</head>

<body>

    <!-- Kop Instansi -->
    @include('exports.partials.kop')

    <!-- Judul Laporan -->
    <h2>Laporan CPU Trend - {{ ucfirst($filter) }}</h2>
    <div class="summary">
        <p><span class="stat">Rentang Waktu:</span> {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}
        </p>
        <p><span class="stat">Tanggal Dicetak:</span> <span class="timestamp">{{ now()->format('Y-m-d H:i:s') }}</span>
        </p>
    </div>

    <hr>

    <!-- Statistik CPU -->
    <div class="summary">
        <p><span class="stat">CPU Minimum:</span> {{ number_format($trendData['minValue'] ?? 0, 2) }}%</p>
        <p><span class="stat">CPU Maksimum:</span> {{ number_format($trendData['maxValue'] ?? 0, 2) }}%</p>
        <p><span class="stat">CPU Rata-rata:</span>
            {{ number_format(($trendData['minValue'] + $trendData['maxValue']) / 2, 2) }}%</p>
    </div>

    <hr>

    <h3>Data Pemakaian CPU</h3>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
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
