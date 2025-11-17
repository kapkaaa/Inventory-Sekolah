@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detail Peminjaman</h1>
            <a href="{{ route('peminjaman.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Kembali
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <!-- Info Peminjaman -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $peminjaman->no_transaksi }}</h2>
                    <p class="text-gray-600 mt-1">Dibuat: {{ $peminjaman->created_at->format('d M Y H:i') }}</p>
                </div>
                <span class="px-4 py-2 text-sm font-bold rounded-full
                    {{ $peminjaman->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $peminjaman->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $peminjaman->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $peminjaman->status === 'borrowed' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $peminjaman->status === 'returned' ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ strtoupper($peminjaman->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">INFORMASI PEMINJAM</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $peminjaman->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $peminjaman->user->email }}</p>
                    @if($peminjaman->user->phone)
                    <p class="text-sm text-gray-600">{{ $peminjaman->user->phone }}</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">JADWAL PEMINJAMAN</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Pinjam:</span>
                            <span class="font-semibold">{{ $peminjaman->tgl_pinjam->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estimasi Kembali:</span>
                            <span class="font-semibold">{{ $peminjaman->tgl_estimasi_kembali->format('d M Y') }}</span>
                        </div>
                        @if($peminjaman->tgl_kembali)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Kembali:</span>
                            <span class="font-semibold text-green-600">{{ $peminjaman->tgl_kembali->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($peminjaman->catatan)
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-500 mb-2">CATATAN</h3>
                <p class="text-gray-700">{{ $peminjaman->catatan }}</p>
            </div>
            @endif

            @if($peminjaman->approved_by)
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-500 mb-2">DIPROSES OLEH</h3>
                <p class="text-gray-700">
                    {{ $peminjaman->approver->name }} - {{ $peminjaman->approved_at->format('d M Y H:i') }}
                </p>
            </div>
            @endif

            @if($peminjaman->total_denda > 0)
            <div class="mt-6 pt-6 border-t">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-red-800 mb-2">DENDA KETERLAMBATAN</h3>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($peminjaman->total_denda, 0, ',', '.') }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Daftar Barang -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Barang yang Dipinjam</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kondisi Awal</th>
                            @if($peminjaman->status === 'returned')
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kondisi Akhir</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($peminjaman->items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $item->inventaris->nama_barang }}</div>
                                <div class="text-sm text-gray-500">{{ $item->inventaris->kategori }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $item->inventaris->kode_barang }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">{{ $item->jumlah }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($item->kondisi_sebelum) }}
                                </span>
                            </td>
                            @if($peminjaman->status === 'returned')
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $item->kondisi_sesudah === 'baik' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($item->kondisi_sesudah) }}
                                </span>
                                @if($item->catatan_item)
                                <p class="text-xs text-gray-500 mt-1">{{ $item->catatan_item }}</p>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Aksi Admin</h3>
            
            <div class="flex flex-wrap gap-3">
                @if($peminjaman->status === 'pending')
                    <form action="{{ route('peminjaman.approve', $peminjaman->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Setujui peminjaman ini?')"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition">
                            ✓ Setujui
                        </button>
                    </form>

                    <button onclick="showRejectModal()" 
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        ✗ Tolak
                    </button>
                @endif

                @if(in_array($peminjaman->status, ['approved', 'borrowed']))
                    <a href="{{ route('peminjaman.return', $peminjaman->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        📥 Proses Pengembalian
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Tolak Peminjaman</h3>
            <form action="{{ route('peminjaman.reject', $peminjaman->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="catatan" 
                              rows="4" 
                              required
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        Tolak Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}
</script>
@endsection