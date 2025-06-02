<x-filament::page>
    <x-filament::card>
        <div x-data="{ tab: 'bandwidth' }">
            <div class="flex space-x-2 mb-4">
                <button class="px-4 py-2 rounded-lg font-semibold"
                    :class="tab === 'bandwidth' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'bandwidth'">
                    Bandwidth Usage
                </button>
                <button class="px-4 py-2 rounded-lg font-semibold"
                    :class="tab === 'device' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'device'">
                    Device Status
                </button>
                <button class="px-4 py-2 rounded-lg font-semibold"
                    :class="tab === 'ping' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'ping'">
                    Ping Latency
                </button>
                {{-- Tambahkan tab lain di sini --}}
            </div>

            <div x-show="tab === 'bandwidth'">
                {{-- Bandwidth Usage Report --}}
                @include('filament.resources.report-resource.pages.bandwidth-usage-report', [
                    'bandwidthData' => $bandwidthData ?? [],
                ])
            </div>
            <div x-show="tab === 'device'">
                {{-- Device Status Report --}}
                @include('filament.resources.report-resource.pages.device-status-report', [
                    'devices' => $devices ?? [],
                ])
            </div>
            <div x-show="tab === 'ping'">
                {{-- Ping Latency Report --}}
                @include('filament.resources.report-resource.pages.ping-latency-report', [
                    'pingData' => $pingData ?? [],
                ])
            </div>
        </div>
    </x-filament::card>
</x-filament::page>
