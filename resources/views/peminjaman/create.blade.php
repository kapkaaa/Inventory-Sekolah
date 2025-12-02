@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Form Peminjaman Inventaris</h1>

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('peminjaman.store') }}" method="POST" id="formPeminjaman" class="bg-white rounded-lg shadow-md p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', date('Y-m-d')) }}" 
                           min="{{ date('Y-m-d') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Estimasi Kembali</label>
                    <input type="date" name="tgl_estimasi_kembali" value="{{ old('tgl_estimasi_kembali') }}" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan / Alasan Peminjaman</label>
                <textarea name="catatan" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('catatan') }}</textarea>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Barang yang Dipinjam</h3>
                    <div class="flex space-x-2">
                        <button type="button" onclick="openScannerModal()" 
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            📷 Scan QR
                        </button>
                        <button type="button" onclick="openModal()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            + Tambah Barang
                        </button>
                    </div>
                </div>

                <div id="selectedItems" class="space-y-3">
                    <!-- Selected items will be added here -->
                </div>

                <div id="emptyState" class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                    Belum ada barang dipilih. Klik "Tambah Barang" atau "Scan QR" untuk memilih.
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('peminjaman.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition">
                    Batal
                </a>
                <button type="submit" id="btnSubmit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pilih Barang -->
<div id="modalBarang" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Pilih Barang</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <input type="text" id="searchBarang" placeholder="Cari barang..." 
                   class="w-full mt-4 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($inventaris as $item)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition cursor-pointer barang-item"
                     data-id="{{ $item->id }}"
                     data-nama="{{ $item->nama_barang }}"
                     data-kode="{{ $item->kode_barang }}"
                     data-stok="{{ $item->jumlah_tersedia }}"
                     onclick="selectBarang(this)">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $item->nama_barang }}</h4>
                            <p class="text-sm text-gray-600">{{ $item->kode_barang }}</p>
                            <p class="text-sm text-gray-500 mt-1">Kategori: {{ $item->kategori }}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                            Stok: {{ $item->jumlah_tersedia }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Scanner -->
<div id="modalScanner" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">📷 Scan QR Code</h3>
                <button onclick="closeScannerModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
        </div>
        <div class="p-6">
            <div id="reader" style="width: 250px; height: 250px; margin: auto;"></div>
            <p id="scanStatus" class="text-center text-gray-600 mt-4">Arahkan kamera ke QR Code barang</p>
        </div>
    </div>
</div>

<style>
    #reader video {
        transform: scaleX(-1) !important;
    }
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let selectedItems = [];
let html5QrCode = null;

window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const scannedCode = urlParams.get('scan');
    
    if (scannedCode) {
        autoSelectFromQRScan(scannedCode);
    }
});

function autoSelectFromQRScan(kodeBarang) {
    const barangElements = document.querySelectorAll('.barang-item');
    
    barangElements.forEach(element => {
        if (element.dataset.kode === kodeBarang) {
            const id = element.dataset.id;
            const nama = element.dataset.nama;
            const kode = element.dataset.kode;
            const stok = parseInt(element.dataset.stok);
            
            if (stok <= 0) {
                alert('Barang ini stoknya kosong, tidak bisa dipinjam!');
                return;
            }
            
            if (!selectedItems.find(item => item.id === id)) {
                selectedItems.push({ id, nama, kode, stok, jumlah: 1 });
                renderSelectedItems();
                
            }
        }
    });
}

function openModal() {
    document.getElementById('modalBarang').classList.remove('hidden');
    document.getElementById('modalBarang').classList.add('flex');
}

function closeModal() {
    document.getElementById('modalBarang').classList.add('hidden');
    document.getElementById('modalBarang').classList.remove('flex');
}

function openScannerModal() {
    document.getElementById('modalScanner').classList.remove('hidden');
    document.getElementById('modalScanner').classList.add('flex');
    startQRScanner();
}

function closeScannerModal() {
    document.getElementById('modalScanner').classList.add('hidden');
    document.getElementById('modalScanner').classList.remove('flex');
    stopQRScanner();
}

function startQRScanner() {
    html5QrCode = new Html5Qrcode("reader");
    
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };
    
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            html5QrCode.start(
                devices[0].id,
                config,
                onScanSuccess,
                onScanError
            ).catch(err => {
                document.getElementById('scanStatus').innerHTML = 
                    '<span class="text-red-600">Error: Tidak dapat mengakses kamera</span>';
                console.error(err);
            });
        } else {
            document.getElementById('scanStatus').innerHTML = 
                '<span class="text-red-600">Error: Kamera tidak ditemukan</span>';
        }
    }).catch(err => {
        document.getElementById('scanStatus').innerHTML = 
            '<span class="text-red-600">Error: ' + err + '</span>';
        console.error(err);
    });
}

function stopQRScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode = null;
        }).catch(err => {
            console.error(err);
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    document.getElementById('scanStatus').innerHTML = 
        '<span class="text-green-600 font-semibold">✓ QR Code terdeteksi! Memproses...</span>';
    
    // Stop scanner
    stopQRScanner();
    
    // Extract kode dari URL atau langsung
    let kodeBarang = decodedText;
    
    try {
        const url = new URL(decodedText);
        const params = new URLSearchParams(url.search);
        kodeBarang = params.get('scan') || decodedText;
    } catch (e) {
    }
    
    // Auto select barang
    autoSelectFromQRScan(kodeBarang);
    
    // Close modal
    setTimeout(() => {
        closeScannerModal();
    }, 500);
}

function onScanError(errorMessage) {
    // Ignore frequent scan errors
}

function selectBarang(element) {
    const id = element.dataset.id;
    const nama = element.dataset.nama;
    const kode = element.dataset.kode;
    const stok = parseInt(element.dataset.stok);

    if (stok <= 0) {
        alert('Stok barang ini kosong dan tidak dapat dipinjam!');
        return;
    }

    if (selectedItems.find(item => item.id === id)) {
        alert('Barang sudah dipilih!');
        return;
    }

    selectedItems.push({ id, nama, kode, stok, jumlah: 1 });
    renderSelectedItems();
    closeModal();
}

function removeItem(index) {
    selectedItems.splice(index, 1);
    renderSelectedItems();
}

function updateJumlah(index, value) {
    const item = selectedItems[index];
    const jumlah = parseInt(value);
    
    if (jumlah > item.stok) {
        alert(`Stok tidak mencukupi! Maksimal: ${item.stok}`);
        return;
    }
    
    if (jumlah < 1) {
        alert('Jumlah minimal 1');
        return;
    }
    
    selectedItems[index].jumlah = jumlah;
    renderSelectedItems();
}

function renderSelectedItems() {
    const container = document.getElementById('selectedItems');
    const emptyState = document.getElementById('emptyState');
    
    if (selectedItems.length === 0) {
        container.innerHTML = '';
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    container.innerHTML = selectedItems.map((item, index) => `
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex justify-between items-center">
            <div class="flex-1">
                <h4 class="font-semibold text-gray-800">${item.nama}</h4>
                <p class="text-sm text-gray-600">Kode: ${item.kode} • Stok tersedia: ${item.stok}</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-700">Jumlah:</label>
                    <input type="number" 
                           value="${item.jumlah}" 
                           min="1" 
                           max="${item.stok}"
                           onchange="updateJumlah(${index}, this.value)"
                           class="w-20 border border-gray-300 rounded px-2 py-1 text-center">
                    <input type="hidden" name="items[${index}][inventaris_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][jumlah]" value="${item.jumlah}">
                </div>
                <button type="button" onclick="removeItem(${index})" 
                        class="text-red-600 hover:text-red-800 font-medium">
                    Hapus
                </button>
            </div>
        </div>
    `).join('');
}

// Search functionality
document.getElementById('searchBarang')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.barang-item').forEach(item => {
        const nama = item.dataset.nama.toLowerCase();
        const kode = item.dataset.kode.toLowerCase();
        item.style.display = (nama.includes(search) || kode.includes(search)) ? 'block' : 'none';
    });
});

// Prevent double submit
document.getElementById('formPeminjaman').addEventListener('submit', function() {
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').textContent = 'Memproses...';
});
</script>
@endsection