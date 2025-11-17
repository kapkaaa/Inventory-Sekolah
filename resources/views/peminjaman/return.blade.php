@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Proses Pengembalian</h1>
            <a href="{{ route('peminjaman.show', $peminjaman->id) }}" class="text-blue-600 hover:text-blue-800">
                ← Kembali
            </a>
        </div>

        <!-- Info Peminjaman -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">No. Transaksi</p>
                    <p class="text-lg font-bold text-gray-900">{{ $peminjaman->no_transaksi }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Peminjam</p>
                    <p class="text-lg font-bold text-gray-900">{{ $peminjaman->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Estimasi Kembali</p>
                    <p class="text-lg font-bold {{ $peminjaman->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $peminjaman->tgl_estimasi_kembali->format('d M Y') }}
                    </p>
                    @if($peminjaman->isOverdue())
                    <p class="text-xs text-red-600 font-medium mt-1">
                        Terlambat {{ \Carbon\Carbon::parse($peminjaman->tgl_estimasi_kembali)->diffInDays(now()) }} hari
                    </p>
                    @endif
                </div>
            </div>

            @if($peminjaman->isOverdue())
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <strong>⚠️ Perhatian:</strong> Peminjaman ini terlambat. Denda akan dikalkulasi otomatis (Rp 5.000/hari).
                </p>
            </div>
            @endif
        </div>

        <!-- Form Pengembalian -->
        <form action="{{ route('peminjaman.process-return', $peminjaman->id) }}" method="POST" id="formReturn">
            @csrf

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Cek Kondisi Barang</h3>
                
                <div class="space-y-6">
                    @foreach($peminjaman->items as $item)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">{{ $item->inventaris->nama_barang }}</h4>
                                <p class="text-sm text-gray-600">{{ $item->inventaris->kode_barang }} • Jumlah: {{ $item->jumlah }}</p>
                                <p class="text-sm text-gray-500 mt-1">Kondisi saat dipinjam: 
                                    <span class="font-medium text-green-600">{{ ucfirst($item->kondisi_sebelum) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kondisi Saat Dikembalikan <span class="text-red-500">*</span>
                                </label>
                                <select name="items[{{ $item->id }}][kondisi_sesudah]" 
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Kondisi</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan (opsional)
                                </label>
                                <input type="text" 
                                       name="items[{{ $item->id }}][catatan_item]"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Catatan kondisi barang...">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Catatan Pengembalian -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Catatan Pengembalian</h3>
                <textarea name="catatan" 
                          rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Catatan tambahan tentang pengembalian ini (opsional)..."></textarea>
            </div>

            <!-- Konfirmasi -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-start mb-4">
                    <input type="checkbox" 
                           id="confirm" 
                           required
                           class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="confirm" class="text-sm text-gray-700">
                        Saya telah memeriksa semua barang dan memastikan kondisi barang sesuai dengan yang tercatat. 
                        Saya memahami bahwa denda keterlambatan akan dikalkulasi otomatis.
                    </label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('peminjaman.show', $peminjaman->id) }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition">
                        Batal
                    </a>
                    <button type="submit" 
                            id="btnSubmit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        Proses Pengembalian
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formReturn').addEventListener('submit', function(e) {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.textContent = 'Memproses...';
});
</script>
@endsection