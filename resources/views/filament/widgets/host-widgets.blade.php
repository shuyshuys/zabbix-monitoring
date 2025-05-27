{{-- filepath: /home/shuya/Documents/Projects/skripsi/zabbix-monitoring/resources/views/filament/widgets/host-widgets.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        <div class="p-4">
            <ul>
                @foreach ($hosts ?? [] as $item)
                    <li>
                        <strong>Host:</strong> {{ $item['host'] }}<br>
                    </li>
                @endforeach
            </ul>
        </div>
        {{-- <div class="mt-4">
            <a href="{{ route('filament.resources.zabbix-resource.index') }}" class="text-blue-500 hover:underline">
                View All Hosts
            </a>
        </div> --}}
    </x-filament::section>
</x-filament-widgets::widget>
