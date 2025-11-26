@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Data Inventaris</h1>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('inventaris.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
            + Tambah Inventaris
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Cari barang..."
                       value="{{ request('search') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <select id="kategoriFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>

            <select id="kondisiFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kondisi</option>
                <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="diperbaiki" {{ request('kondisi') == 'diperbaiki' ? 'selected' : '' }}>Diperbaiki</option>
            </select>

            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition" onclick="clearAllFilters()">
                Clear Filter
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($inventaris as $item)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->kode_barang }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->nama_barang }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->kategori }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="font-semibold">{{ $item->jumlah_tersedia }}</span> / {{ $item->jumlah_total }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $item->kondisi === 'baik' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $item->kondisi === 'rusak' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $item->kondisi === 'diperbaiki' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst($item->kondisi) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('inventaris.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('inventaris.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('inventaris.destroy', $item->id) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Yakin hapus inventaris ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data inventaris</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $inventaris->links() }}
    </div>

    <!-- Live Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const kategoriFilter = document.getElementById('kategoriFilter');
            const kondisiFilter = document.getElementById('kondisiFilter');

            // Add event listeners with debounce
            searchInput.addEventListener('input', debounce(function() {
                performLiveSearch();
            }, 300));

            kategoriFilter.addEventListener('change', function() {
                performLiveSearch();
            });

            kondisiFilter.addEventListener('change', function() {
                performLiveSearch();
            });

            // Handle Enter key press on search input
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performLiveSearch();
                }
            });
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function performLiveSearch() {
            const searchQuery = document.getElementById('searchInput').value;
            const kategori = document.getElementById('kategoriFilter').value;
            const kondisi = document.getElementById('kondisiFilter').value;
            const url = new URL(window.location);

            // Set search parameter
            if (searchQuery) {
                url.searchParams.set('search', searchQuery);
            } else {
                url.searchParams.delete('search');
            }

            // Set kategori parameter
            if (kategori) {
                url.searchParams.set('kategori', kategori);
            } else {
                url.searchParams.delete('kategori');
            }

            // Set kondisi parameter
            if (kondisi) {
                url.searchParams.set('kondisi', kondisi);
            } else {
                url.searchParams.delete('kondisi');
            }

            // Remove page parameter to start from first page
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('kategoriFilter').value = '';
            document.getElementById('kondisiFilter').value = '';

            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('kategori');
            url.searchParams.delete('kondisi');
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }
    </script>
</div>
@endsection