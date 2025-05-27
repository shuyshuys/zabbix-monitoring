<x-filament-widgets::widget>
    <x-filament::section>
        <form wire:submit.prevent="runTraceroute">
            <div class="space-y-4">
                <div>
                    <button type="submit"
                        class="w-full px-3 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400 transition">
                        Jalankan Traceroute
                    </button>
                </div>
                <div>
                    <label class="block font-medium mb-1 dark:text-white">Hasil Traceroute</label>
                    <textarea class="filament-forms-input block w-full dark:bg-gray-900 dark:text-white" rows="10" readonly>{{ $result }}</textarea>
                </div>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
