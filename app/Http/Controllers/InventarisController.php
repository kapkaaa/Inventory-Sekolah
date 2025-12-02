<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventaris::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_barang', 'like', "%{$request->search}%")
                  ->orWhere('kode_barang', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $inventaris = $query->latest()->paginate(15);
        $kategoris = Inventaris::distinct()->pluck('kategori')->filter();

        return view('inventaris.index', compact('inventaris', 'kategoris'));
    }

    public function create()
    {
        return view('inventaris.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jumlah_total' => 'required|integer|min:0',
            'kondisi' => 'required|in:baik,rusak,diperbaiki',
            'lokasi' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Generate kode barang unik
        $kode = 'INV-' . strtoupper(Str::random(8));
        $qrUrl = route('peminjaman.create', ['scan' => $kode]);
        
        // Generate QR Code image
        $qrImage = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($qrUrl);
        
        // Save QR Code ke storage
        $qrPath = 'qrcodes/' . $kode . '.svg';
        Storage::disk('public')->put($qrPath, $qrImage);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('inventaris', 'public');
        }

        // Simpan inventaris ke database
        $inventaris = Inventaris::create([
            'kode_barang' => $kode,
            'nama_barang' => $validated['nama_barang'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'jumlah_total' => $validated['jumlah_total'],
            'jumlah_tersedia' => $validated['jumlah_total'],
            'kondisi' => $validated['kondisi'],
            'lokasi' => $validated['lokasi'],
            'qr_code' => $qrPath,
            'foto' => $fotoPath,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_inventaris',
            'detail' => json_encode([
                'inventaris_id' => $inventaris->id, 
                'kode' => $kode,
                'nama_barang' => $inventaris->nama_barang
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('inventaris.index')
            ->with('success', 'Inventaris berhasil ditambahkan dengan QR Code!');
    }

    public function show($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        return view('inventaris.show', compact('inventaris'));
    }

    public function edit($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        return view('inventaris.edit', compact('inventaris'));
    }

    public function update(Request $request, $id)
    {
        $inventaris = Inventaris::findOrFail($id);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jumlah_total' => 'required|integer|min:0',
            'kondisi' => 'required|in:baik,rusak,diperbaiki',
            'lokasi' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle foto update
        if ($request->hasFile('foto')) {
            if ($inventaris->foto) {
                Storage::disk('public')->delete($inventaris->foto);
            }
            $validated['foto'] = $request->file('foto')->store('inventaris', 'public');
        }

        $inventaris->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_inventaris',
            'detail' => json_encode(['inventaris_id' => $inventaris->id]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('inventaris.index')
            ->with('success', 'Inventaris berhasil diupdate!');
    }

    public function destroy($id)
    {
        $inventaris = Inventaris::findOrFail($id);

        // Delete files
        if ($inventaris->foto) {
            Storage::disk('public')->delete($inventaris->foto);
        }
        if ($inventaris->qr_code) {
            Storage::disk('public')->delete($inventaris->qr_code);
        }

        $inventaris->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_inventaris',
            'detail' => json_encode(['kode_barang' => $inventaris->kode_barang]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('inventaris.index')
            ->with('success', 'Inventaris berhasil dihapus!');
    }

    /**
     * Download QR Code
     */
    public function downloadQr($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        
        if (!$inventaris->qr_code || !Storage::disk('public')->exists($inventaris->qr_code)) {
            return redirect()->back()->with('error', 'QR Code tidak ditemukan!');
        }

        $filePath = storage_path('app/public/' . $inventaris->qr_code);
        $fileName = $inventaris->kode_barang . '_QRCode.png';

        return response()->download($filePath, $fileName);
    }

    /**
     * Print QR Code
     */
    public function printQr($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        return view('inventaris.print-qr', compact('inventaris'));
    }
}