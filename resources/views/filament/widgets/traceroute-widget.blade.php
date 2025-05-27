<x-filament-widgets::widget>
    <x-filament::section>
        <form wire:submit.prevent="runTraceroute">
            <div class="space-y-4">
                <div>
                    <label class="block font-medium mb-1 dark:text-white">Pilih Host</label>
                    <select wire:model="target"
                        class="filament-forms-input block w-full dark:bg-gray-900 dark:text-white">
                        <option value="">-- Pilih --</option>
                        <option value="172.16.143.154">mikrotik-fik2</option>
                        <option value="192.166.1.254">mikrotik-gkb-lt1</option>
                        <option value="192.166.7.210">mikrotik-gkb-lt2</option>
                        <option value="192.166.3.249">mikrotik-gkb-lt3</option>
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
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
