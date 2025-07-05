{{-- filepath: resources/views/exports/dhcp-lease-trend-report.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>DHCP Lease Trend Report</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #1F2937;
            padding: 2rem;
        }

        .stat {
            font-weight: 600;
            color: #111827;
        }

        .timestamp {
            font-size: 11px;
            color: #6B7280;
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
    </style>
</head>

<body>
    {{-- Kop Instansi --}}
    @include('exports.partials.kop')

    {{-- Judul Laporan --}}
    <h2 class="text-lg font-bold mt-8 mb-2">Laporan DHCP Lease Trend - {{ ucfirst($filter) }}</h2>
    <div class="mb-2">
        <span class="stat">Rentang Waktu:</span> {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}
    </div>
    <div class="mb-2">
        <span class="stat">Tanggal Dicetak:</span>
        <span class="timestamp">{{ now()->format('Y-m-d H:i:s') }}</span>
    </div>

    <hr class="my-4 border-gray-300">

    <div class="mb-2">
        <span class="stat">Max Lease Count:</span> {{ number_format($trendData['max'] ?? 0) }}
    </div>
    <div class="mb-2">
        <span class="stat">Min Lease Count:</span> {{ number_format($trendData['min'] ?? 0) }}
    </div>

    <hr class="my-4 border-gray-300">

    <h3 class="text-base font-semibold mb-2">Data DHCP Lease</h3>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
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
