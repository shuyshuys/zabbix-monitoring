<x-filament-widgets::widget>
    <x-filament::section>
        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        <div class="p-4">
            <h2 class="text-lg font-bold mb-2">Data from API</h2>
            <ul>
                @foreach ($data->result ?? [] as $item)
                    <li>
                        <strong>Host:</strong> {{ $item['host'] }}<br>
                        <strong>Name:</strong> {{ $item['name'] }}<br>
                        <strong>Status:</strong> {{ $item['status'] }}<br>
                        <strong>Host ID:</strong> {{ $item['hostid'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
