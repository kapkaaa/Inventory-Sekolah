<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'items.inventaris']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->latest()->paginate(20);

        // Statistics
        $totalPeminjaman = Peminjaman::count();
        $totalApproved = Peminjaman::where('status', 'approved')->count();
        $totalReturned = Peminjaman::where('status', 'returned')->count();
        $totalDenda = Peminjaman::where('status', 'returned')->sum('total_denda');

        // Most borrowed items
        $mostBorrowed = DB::table('peminjaman_items')
            ->join('inventaris', 'peminjaman_items.inventaris_id', '=', 'inventaris.id')
            ->join('peminjaman', 'peminjaman_items.peminjaman_id', '=', 'peminjaman.id')
            ->whereIn('peminjaman.status', ['approved', 'borrowed', 'returned'])
            ->select('inventaris.nama_barang', 'inventaris.kode_barang', 
                     DB::raw('SUM(peminjaman_items.jumlah) as total_pinjam'))
            ->groupBy('inventaris.id', 'inventaris.nama_barang', 'inventaris.kode_barang')
            ->orderByDesc('total_pinjam')
            ->limit(10)
            ->get();

        // Damaged items
        $damagedItems = PeminjamanItem::with(['inventaris', 'peminjaman.user'])
            ->where('kondisi_sesudah', 'rusak')
            ->latest()
            ->limit(10)
            ->get();

        return view('laporan.index', compact(
            'peminjaman',
            'totalPeminjaman',
            'totalApproved',
            'totalReturned',
            'totalDenda',
            'mostBorrowed',
            'damagedItems'
        ));
    }

    public function exportExcel(Request $request)
    {
        $query = Peminjaman::with(['user', 'items.inventaris']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->latest()->get();

        return Excel::download(new \App\Exports\PeminjamanExport($peminjaman), 
                              'laporan_peminjaman_' . date('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Peminjaman::with(['user', 'items.inventaris']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->latest()->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('peminjaman'));
        
        return $pdf->download('laporan_peminjaman_' . date('Y-m-d') . '.pdf');
    }
}