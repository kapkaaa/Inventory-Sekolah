<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Statistics
        $totalInventaris = Inventaris::count();
        $barangTersedia = Inventaris::where('jumlah_tersedia', '>', 0)->count();
        $sedangDipinjam = Peminjaman::whereIn('status', ['approved', 'borrowed'])->count();
        $barangRusak = Inventaris::where('kondisi', 'rusak')->count();

        if ($user->role === 'admin') {
            // Admin dashboard data
            $pendingPeminjaman = Peminjaman::with(['user', 'items'])
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get();

            $overduePeminjaman = Peminjaman::with(['user', 'items'])
                ->whereIn('status', ['approved', 'borrowed'])
                ->where('tgl_estimasi_kembali', '<', now())
                ->latest()
                ->limit(5)
                ->get();

            return view('dashboard', compact(
                'totalInventaris',
                'barangTersedia',
                'sedangDipinjam',
                'barangRusak',
                'pendingPeminjaman',
                'overduePeminjaman'
            ));
        } else {
            // User dashboard data
            $myActivePeminjaman = Peminjaman::with(['items.inventaris'])
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved', 'borrowed'])
                ->latest()
                ->limit(5)
                ->get();

            return view('dashboard', compact(
                'totalInventaris',
                'barangTersedia',
                'sedangDipinjam',
                'barangRusak',
                'myActivePeminjaman'
            ));
        }
    }
}