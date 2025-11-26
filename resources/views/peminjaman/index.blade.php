@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Peminjaman</h1>
        <a href="{{ route('peminjaman.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
            + Ajukan Peminjaman
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text"
                       id="searchInput"
                       placeholder="Cari nomor transaksi..."
                       value="{{ request('search') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <select id="statusFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                    @if(auth()->user()->isAdmin())
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($peminjaman as $p)
                <tr class="{{ $p->isOverdue() && in_array($p->status, ['approved', 'borrowed']) ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $p->no_transaksi }}</div>
                        <div class="text-xs text-gray-500">{{ $p->created_at->format('d M Y H:i') }}</div>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $p->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $p->user->email }}</div>
                    </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $p->tgl_pinjam->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">s/d {{ $p->tgl_estimasi_kembali->format('d M Y') }}</div>
                        @if($p->isOverdue() && in_array($p->status, ['approved', 'borrowed']))
                        <div class="text-xs text-red-600 font-semibold mt-1">
                            ⚠️ Terlambat {{ \Carbon\Carbon::parse($p->tgl_estimasi_kembali)->diffInDays(now()) }} hari
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm font-semibold text-gray-900">{{ $p->items->count() }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $p->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $p->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $p->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $p->status === 'borrowed' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $p->status === 'returned' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('peminjaman.show', $p->id) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            Detail →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">Tidak ada data peminjaman</p>
                            <p class="text-sm text-gray-400 mt-1">Mulai ajukan peminjaman dengan klik tombol di atas</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $peminjaman->links() }}
    </div>

    <!-- Live Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');

            // Add event listeners with debounce
            searchInput.addEventListener('input', debounce(function() {
                performLiveSearch();
            }, 300));

            statusFilter.addEventListener('change', function() {
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
            const status = document.getElementById('statusFilter').value;
            const url = new URL(window.location);

            // Set search parameter
            if (searchQuery) {
                url.searchParams.set('search', searchQuery);
            } else {
                url.searchParams.delete('search');
            }

            // Set status parameter
            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }

            // Remove page parameter to start from first page
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';

            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('status');
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }
    </script>
</div>
@endsection