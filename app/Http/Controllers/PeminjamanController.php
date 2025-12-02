<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Inventaris;
use App\Models\ActivityLog;
use App\Http\Requests\StorePeminjamanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'items.inventaris']);

        // Filter by user for non-admin
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('no_transaksi', 'like', "%{$request->search}%");
        }

        $peminjaman = $query->latest()->paginate(15);

        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        $inventaris = Inventaris::where('jumlah_tersedia', '>', 0)
            ->where('kondisi', 'baik')
            ->get();

        return view('peminjaman.create', compact('inventaris'));
    }

    public function store(StorePeminjamanRequest $request)
    {
        try {
            DB::beginTransaction();

            // Generate nomor transaksi
            $noTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Create peminjaman
            $peminjaman = Peminjaman::create([
                'no_transaksi' => $noTransaksi,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_estimasi_kembali' => $request->tgl_estimasi_kembali,
                'catatan' => $request->catatan,
            ]);

            // Process items
            foreach ($request->items as $item) {
                $inventaris = Inventaris::lockForUpdate()->findOrFail($item['inventaris_id']);

                // Double check stock availability
                if (!$inventaris->isAvailable($item['jumlah'])) {
                    throw new \Exception("Stok {$inventaris->nama_barang} tidak mencukupi.");
                }

                // Create peminjaman item
                PeminjamanItem::create([
                    'peminjaman_id' => $peminjaman->id,
                    'inventaris_id' => $inventaris->id,
                    'jumlah' => $item['jumlah'],
                    'kondisi_sebelum' => $inventaris->kondisi,
                ]);

                // Decrement stock
                $inventaris->decrement('jumlah_tersedia', $item['jumlah']);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'create_peminjaman',
                'detail' => json_encode(['peminjaman_id' => $peminjaman->id, 'no_transaksi' => $noTransaksi]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat peminjaman: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with(['user', 'items.inventaris', 'approver'])
            ->findOrFail($id);

        // Authorization check
        if (auth()->user()->role !== 'admin' && $peminjaman->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        return view('peminjaman.show', compact('peminjaman'));
    }

    public function approve(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status !== 'pending') {
            return redirect()->back()->with('error', 'Peminjaman sudah diproses.');
        }

        $peminjaman->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_peminjaman',
            'detail' => json_encode(['peminjaman_id' => $peminjaman->id]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);


        return redirect()->back()->with('success', 'Peminjaman berhasil disetujui!');
    }

    public function reject(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('items')->findOrFail($id);

            if ($peminjaman->status !== 'pending') {
                return redirect()->back()->with('error', 'Peminjaman sudah diproses.');
            }

            // Return stock
            foreach ($peminjaman->items as $item) {
                $inventaris = Inventaris::lockForUpdate()->findOrFail($item->inventaris_id);
                $inventaris->increment('jumlah_tersedia', $item->jumlah);
            }

            $peminjaman->update([
                'status' => 'rejected',
                'catatan' => $request->catatan,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'reject_peminjaman',
                'detail' => json_encode(['peminjaman_id' => $peminjaman->id]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Peminjaman berhasil ditolak!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menolak peminjaman: ' . $e->getMessage());
        }
    }

    public function returnForm($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $peminjaman = Peminjaman::with(['user', 'items.inventaris'])->findOrFail($id);

        if (!in_array($peminjaman->status, ['approved', 'borrowed'])) {
            return redirect()->back()->with('error', 'Peminjaman tidak dapat dikembalikan.');
        }

        return view('peminjaman.return', compact('peminjaman'));
    }

    public function processReturn(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'items.*.kondisi_sesudah' => 'required|in:baik,rusak',
            'items.*.catatan_item' => 'nullable|string|max:500',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::with('items')->findOrFail($id);

            if (!in_array($peminjaman->status, ['approved', 'borrowed'])) {
                throw new \Exception('Peminjaman tidak dapat dikembalikan.');
            }

            // Update items and return stock
            foreach ($request->items as $itemId => $itemData) {
                $peminjamanItem = PeminjamanItem::findOrFail($itemId);
                $inventaris = Inventaris::lockForUpdate()->findOrFail($peminjamanItem->inventaris_id);

                $peminjamanItem->update([
                    'kondisi_sesudah' => $itemData['kondisi_sesudah'],
                    'catatan_item' => $itemData['catatan_item'] ?? null,
                ]);

                // Return stock
                $inventaris->increment('jumlah_tersedia', $peminjamanItem->jumlah);

                // Update kondisi if damaged
                if ($itemData['kondisi_sesudah'] === 'rusak') {
                    $inventaris->update(['kondisi' => 'rusak']);
                }
            }

            // Calculate denda
            $tglKembali = now();
            $peminjaman->tgl_kembali = $tglKembali;
            $denda = $peminjaman->calculateDenda();

            $peminjaman->update([
                'status' => 'returned',
                'tgl_kembali' => $tglKembali,
                'total_denda' => $denda,
                'catatan' => $request->catatan,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'return_peminjaman',
                'detail' => json_encode([
                    'peminjaman_id' => $peminjaman->id,
                    'denda' => $denda,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('peminjaman.show', $id)
                ->with('success', 'Pengembalian berhasil diproses! Denda: Rp ' . number_format($denda, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}