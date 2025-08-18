<div 
    x-data="{ 
        open: false,
        scanner: null
    }" 
    x-on:toggle-scanner.window="
        open = !open;
        if (open) {
            $nextTick(() => {
                scanner = new Html5Qrcode('reader');
                scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 }
                    },
                    (decodedText) => {
                        Livewire.dispatch('scanResult', { decodedText });
                        scanner.stop();
                        open = false;
                    },
                    (error) => {
                        // silent error
                    }
                );
            });
        } else if (scanner) {
            scanner.stop();
        }
    "
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full relative">
        <h2 class="text-lg font-semibold mb-4">Scan Barcode</h2>
        <div id="reader" class="w-full"></div>
        <button @click="open = false; if (scanner) scanner.stop();" 
                class="absolute top-0 right-0 m-2 text-gray-600 hover:text-gray-900 dark:text-gray-400">
            &times;
        </button>
    </div>
</div>
