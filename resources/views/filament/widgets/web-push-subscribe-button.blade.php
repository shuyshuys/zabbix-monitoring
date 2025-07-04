<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <x-filament::button color="primary" onclick="window.FilamentWebPush && window.FilamentWebPush.subscribe()">
            Aktifkan Notifikasi Webpush
        </x-filament::button>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                if (window.FilamentWebPush) {
                    window.FilamentWebPush.isSubscribed().then(function(isSubscribed) {
                        if (isSubscribed) {
                            console.log('Web Push is already subscribed.');
                        } else {
                            console.log('Web Push is not subscribed yet.');
                        }
                    }).catch(function(error) {
                        console.error('Error checking Web Push subscription:', error);
                    });
                }
            });
        </script>
        {{-- End of widget content --}}
    </x-filament::section>
</x-filament-widgets::widget>
