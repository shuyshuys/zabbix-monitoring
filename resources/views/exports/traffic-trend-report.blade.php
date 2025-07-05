{{-- filepath: resources/views/exports/traffic-trend-report.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Traffic Trend Report</title>
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
    {{-- Kop Instansi --}}
    @include('exports.partials.kop')

    {{-- Judul Laporan --}}
    <h2>Laporan Traffic Trend - {{ ucfirst($filter) }}</h2>
    <div class="summary">
        <p><span class="stat">Rentang Waktu:</span> {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}
        </p>
        <p><span class="stat">Tanggal Dicetak:</span> <span class="timestamp">{{ now()->format('Y-m-d H:i:s') }}</span>
        </p>
    </div>

    <hr>

    <div class="summary">
        <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 0.75rem;">Statistik Traffic</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
            <!-- Inbound -->
            <div
                style="flex: 1 1 45%; background-color: #F3F4F6; padding: 1rem; border-radius: 8px; border: 1px solid #D1D5DB;">
                <h4 style="margin: 0 0 0.5rem 0; font-weight: 600; color: #1D4ED8;">Inbound</h4>
                <p style="margin: 0.25rem 0;"><b>Minimum:</b> {{ number_format($trendData['minValueIn'] ?? 0, 2) }} Mbps
                </p>
                <p style="margin: 0.25rem 0;"><b>Maksimum:</b> {{ number_format($trendData['maxValueIn'] ?? 0, 2) }}
                    Mbps</p>
                <p style="margin: 0.25rem 0;"><b>Rata-rata:</b> {{ number_format($trendData['avgValueIn'] ?? 0, 2) }}
                    Mbps</p>
            </div>

            <!-- Outbound -->
            <div
                style="flex: 1 1 45%; background-color: #F3F4F6; padding: 1rem; border-radius: 8px; border: 1px solid #D1D5DB;">
                <h4 style="margin: 0 0 0.5rem 0; font-weight: 600; color: #047857;">Outbound</h4>
                <p style="margin: 0.25rem 0;"><b>Minimum:</b> {{ number_format($trendData['minValueOut'] ?? 0, 2) }}
                    Mbps</p>
                <p style="margin: 0.25rem 0;"><b>Maksimum:</b> {{ number_format($trendData['maxValueOut'] ?? 0, 2) }}
                    Mbps</p>
                <p style="margin: 0.25rem 0;"><b>Rata-rata:</b> {{ number_format($trendData['avgValueOut'] ?? 0, 2) }}
                    Mbps</p>
            </div>
        </div>
    </div>

    <hr>

    <h3>Data Traffic</h3>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Inbound (Mbps)</th>
                <th>Outbound (Mbps)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $i => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($dataIn[$i] ?? 0, 2) }}</td>
                    <td>{{ number_format($dataOut[$i] ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
