@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Scan QR Code Inventaris</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Scanner Container -->
            <div id="reader" class="w-full mb-6" style="min-height: 400px;"></div>

            <!-- Status -->
            <div id="status" class="text-center text-gray-600 mb-4">
                Arahkan kamera ke QR Code
            </div>

            <!-- Manual Input -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Atau Masukkan Kode Manual</h3>
                <form action="{{ route('inventaris.search') }}" method="GET" class="flex gap-2">
                    <input type="text" 
                           name="kode" 
                           placeholder="Masukkan kode barang..."
                           class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        Cari
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isScanning = false;

function onScanSuccess(decodedText, decodedResult) {
    if (isScanning) return;
    isScanning = true;

    document.getElementById('status').innerHTML = 
        '<span class="text-green-600 font-semibold">✓ QR Code terdeteksi! Memproses...</span>';

    // Stop scanning
    html5QrCode.stop().then(() => {
        // Extract kode from URL or use direct code
        let kode = decodedText;
        
        // If it's a URL, extract the kode parameter
        try {
            const url = new URL(decodedText);
            const pathParts = url.pathname.split('/');
            kode = pathParts[pathParts.length - 1] || decodedText;
        } catch (e) {
            // Not a URL, use as is
        }

        // Redirect to inventory detail or search
        window.location.href = `/inventaris/search?kode=${encodeURIComponent(kode)}`;
    }).catch(err => {
        console.error('Error stopping scanner:', err);
        window.location.href = `/inventaris/search?kode=${encodeURIComponent(decodedText)}`;
    });
}

function onScanError(errorMessage) {
    // Ignore scan errors (happens frequently during scanning)
    // console.log('Scan error:', errorMessage);
}

// Start scanner
document.addEventListener('DOMContentLoaded', function() {
    html5QrCode = new Html5Qrcode("reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const cameraId = devices[0].id;
            
            html5QrCode.start(
                cameraId,
                config,
                onScanSuccess,
                onScanError
            ).catch(err => {
                document.getElementById('status').innerHTML = 
                    '<span class="text-red-600">Error: Tidak dapat mengakses kamera. ' + err + '</span>';
            });
        } else {
            document.getElementById('status').innerHTML = 
                '<span class="text-red-600">Error: Kamera tidak ditemukan</span>';
        }
    }).catch(err => {
        document.getElementById('status').innerHTML = 
            '<span class="text-red-600">Error: ' + err + '</span>';
    });
});

// Cleanup on page leave
window.addEventListener('beforeunload', function() {
    if (html5QrCode) {
        html5QrCode.stop().catch(err => console.error('Error stopping:', err));
    }
});
</script>
@endsection