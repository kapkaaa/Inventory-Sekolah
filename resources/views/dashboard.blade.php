@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard</h1>

    @if(auth()->user()->role === 'admin')
    <!-- Admin Section -->
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Inventaris</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalInventaris }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Barang Tersedia</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $barangTersedia }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $sedangDipinjam }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Barang Rusak</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $barangRusak }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Peminjaman Menunggu Persetujuan</h2>
            <div class="space-y-3">
                @forelse($pendingPeminjaman as $p)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $p->no_transaksi }}</p>
                            <p class="text-sm text-gray-600">{{ $p->items->count() }} barang</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Kembali: {{ \Carbon\Carbon::parse($p->tgl_estimasi_kembali)->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $p->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $p->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $p->status === 'borrowed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                            <a href="{{ route('peminjaman.show', $p->id) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                Detail →
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">Tidak ada peminjaman aktif</p>
                @endforelse
            </div>
        </div>

        <!-- Overdue Returns -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Peminjaman Terlambat</h2>
            <div class="space-y-3">
                @forelse($overduePeminjaman as $p)
                <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $p->no_transaksi }}</p>
                            <p class="text-sm text-gray-600">{{ $p->user->name }}</p>
                            <p class="text-xs text-red-600 mt-1 font-medium">
                                Terlambat {{ \Carbon\Carbon::parse($p->tgl_estimasi_kembali)->diffInDays(now()) }} hari
                            </p>
                        </div>
                        <a href="{{ route('peminjaman.show', $p->id) }}" 
                           class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Proses →
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">Tidak ada peminjaman terlambat</p>
                @endforelse
            </div>
        </div>

    </div>

    @else
    <!-- User Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Peminjaman Aktif Saya</h2>
        <div class="space-y-3">
            @forelse($myActivePeminjaman as $p)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $p->no_transaksi }}</p>
                        <p class="text-sm text-gray-600">{{ $p->items->count() }} barang</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $p->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('peminjaman.show', $p->id) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Lihat Detail →
                    </a>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4">Tidak ada peminjaman aktif</p>
            @endforelse
        </div>
    </div>

    @endif

</div>
@endsection
