@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detail Inventaris</h1>
            <a href="{{ route('inventaris.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Kembali
            </a>
        </div>

        <!-- Detail Box -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                
                <!-- Foto Barang -->
                <div class="md:w-1/3 bg-gray-100 flex items-center justify-center p-8">
                    @if($inventaris->foto)
                        <img src="{{ asset('storage/' . $inventaris->foto) }}" 
                             alt="{{ $inventaris->nama_barang }}" 
                             class="w-full h-auto rounded-lg shadow">
                    @else
                        <div class="text-center text-gray-400">
                            <svg class="w-32 h-32 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-2">Tidak ada foto</p>
                        </div>
                    @endif
                </div>

                <!-- Detail Informasi -->
                <div class="md:w-2/3 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $inventaris->nama_barang }}</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Kode Barang</span>
                            <span class="font-semibold text-gray-900">{{ $inventaris->kode_barang }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Kategori</span>
                            <span class="font-semibold text-gray-900">{{ $inventaris->kategori }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Jumlah Total</span>
                            <span class="font-semibold text-gray-900">{{ $inventaris->jumlah_total }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Stok Tersedia</span>
                            <span class="font-bold {{ $inventaris->jumlah_tersedia > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $inventaris->jumlah_tersedia }}
                            </span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Kondisi</span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $inventaris->kondisi === 'baik' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $inventaris->kondisi === 'rusak' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $inventaris->kondisi === 'diperbaiki' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst($inventaris->kondisi) }}
                            </span>
                        </div>

                        @if($inventaris->lokasi)
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Lokasi</span>
                            <span class="font-semibold text-gray-900">{{ $inventaris->lokasi }}</span>
                        </div>
                        @endif

                        @if($inventaris->deskripsi)
                        <div class="pt-2">
                            <span class="text-gray-600 font-medium">Deskripsi:</span>
                            <p class="text-gray-700 mt-1">{{ $inventaris->deskripsi }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- QR Code -->
                    @if($inventaris->qr_code)
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">QR Code</h3>
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('storage/' . $inventaris->qr_code) }}" 
                                 alt="QR Code" class="w-32 h-32 border rounded">
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">Scan QR code ini untuk akses cepat</p>

                                <div class="flex space-x-2">
                                    <a href="{{ route('inventaris.print-qr', $inventaris->id) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">🖨️ Print QR Code</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-6 pt-6 border-t flex flex-wrap gap-3">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('inventaris.edit', $inventaris->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">Edit</a>

                            <form action="{{ route('inventaris.destroy', $inventaris->id) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus inventaris ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
                                    Hapus
                                </button>
                            </form>
                        @endif
                        
                        @if($inventaris->jumlah_tersedia > 0 && $inventaris->kondisi === 'baik')
                            <a href="{{ route('peminjaman.create') }}?scan={{ $inventaris->kode_barang }}" 
                               class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                                Pinjam Barang
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <!-- Riwayat -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Riwayat Peminjaman</h3>

            @php
                $riwayat = $inventaris->peminjamanItems()
                    ->with(['peminjaman.user'])
                    ->latest()
                    ->limit(10)
                    ->get();
            @endphp
            
            @if($riwayat->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($riwayat as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $item->peminjaman->no_transaksi }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->peminjaman->user->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->jumlah }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $item->peminjaman->tgl_pinjam->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $item->peminjaman->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $item->peminjaman->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $item->peminjaman->status === 'returned' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($item->peminjaman->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            @else
                <p class="text-center text-gray-500 py-4">Belum ada riwayat peminjaman</p>
            @endif

        </div>

    </div>
</div>


@endsection
