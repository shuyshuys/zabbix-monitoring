{{-- filepath: resources/views/livewire/traffic-trend-report.blade.php --}}
<div>
    <form wire:submit.prevent="downloadPdf" class="flex items-center gap-4 mb-4">
        <select wire:model="filter" class="border rounded px-2 py-1">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="last_week">Last Week</option>
            <option value="last_month">Last Month</option>
            <option value="last_3_month">Last 3 Month</option>
            <option value="last_6_month">Last 6 Month</option>
            <option value="last_year">Last Year</option>

        </select>
        {{-- <button type="submit" class="bg-primary-600 text-white px-4 py-1 rounded">Download PDF</button> --}}
        <x-filament::button color="primary" type="submit" class="mb-0">
            Download PDF
        </x-filament::button>
    </form>

    <div class="mb-4">
        <div>Range: {{ $trendData['from'] ?? '-' }} - {{ $trendData['till'] ?? '-' }}</div>
        <div>Total Inbound: <b>{{ number_format($trendData['total_in'] ?? 0) }} MB</b></div>
        <div>Total Outbound: <b>{{ number_format($trendData['total_out'] ?? 0) }} MB</b></div>
    </div>
</div>
