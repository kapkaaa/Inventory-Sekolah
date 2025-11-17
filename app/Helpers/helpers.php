<?php

/**
 * Helper functions untuk aplikasi
 */

if (!function_exists('formatRupiah')) {
    /**
     * Format angka ke format Rupiah
     */
    function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format tanggal ke format Indonesia
     */
    function formatDate($date, $format = 'd M Y')
    {
        if (!$date) return '-';
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format tanggal dan waktu ke format Indonesia
     */
    function formatDateTime($datetime)
    {
        if (!$datetime) return '-';
        return \Carbon\Carbon::parse($datetime)->format('d M Y H:i');
    }
}

if (!function_exists('diffForHumans')) {
    /**
     * Format tanggal relative (1 jam lalu, 2 hari lalu, dst)
     */
    function diffForHumans($date)
    {
        if (!$date) return '-';
        return \Carbon\Carbon::parse($date)->diffForHumans();
    }
}

if (!function_exists('isOverdue')) {
    /**
     * Check apakah tanggal sudah terlewat
     */
    function isOverdue($date)
    {
        if (!$date) return false;
        return \Carbon\Carbon::parse($date)->isPast();
    }
}

if (!function_exists('statusBadge')) {
    /**
     * Generate HTML badge untuk status
     */
    function statusBadge($status)
    {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'borrowed' => 'bg-blue-100 text-blue-800',
            'returned' => 'bg-gray-100 text-gray-800',
        ];

        $class = $classes[$status] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $class . '">' 
               . ucfirst($status) . '</span>';
    }
}

if (!function_exists('kondisiBadge')) {
    /**
     * Generate HTML badge untuk kondisi barang
     */
    function kondisiBadge($kondisi)
    {
        $classes = [
            'baik' => 'bg-green-100 text-green-800',
            'rusak' => 'bg-red-100 text-red-800',
            'diperbaiki' => 'bg-yellow-100 text-yellow-800',
        ];

        $class = $classes[$kondisi] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $class . '">' 
               . ucfirst($kondisi) . '</span>';
    }
}

if (!function_exists('generateNoTransaksi')) {
    /**
     * Generate nomor transaksi unik
     */
    function generateNoTransaksi($prefix = 'TRX')
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

if (!function_exists('generateKodeBarang')) {
    /**
     * Generate kode barang unik
     */
    function generateKodeBarang($prefix = 'INV')
    {
        return $prefix . '-' . strtoupper(\Illuminate\Support\Str::random(8));
    }
}

if (!function_exists('calculateDenda')) {
    /**
     * Hitung denda keterlambatan
     */
    function calculateDenda($tglKembali, $tglEstimasi, $dendaPerHari = 5000)
    {
        $kembali = \Carbon\Carbon::parse($tglKembali);
        $estimasi = \Carbon\Carbon::parse($tglEstimasi);

        if ($kembali->lte($estimasi)) {
            return 0;
        }

        $hariTerlambat = $kembali->diffInDays($estimasi);
        return $hariTerlambat * $dendaPerHari;
    }
}

if (!function_exists('logActivity')) {
    /**
     * Shorthand untuk log activity
     */
    function logActivity($action, $detail = [])
    {
        return \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'detail' => json_encode($detail),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check apakah user adalah admin
     */
    function isAdmin()
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
}

if (!function_exists('getKategoriList')) {
    /**
     * Get list kategori inventaris
     */
    function getKategoriList()
    {
        return [
            'Elektronik',
            'Olahraga',
            'Alat Tulis',
            'Multimedia',
            'Lab',
            'Furnitur',
            'Lainnya',
        ];
    }
}

if (!function_exists('getStatusList')) {
    /**
     * Get list status peminjaman
     */
    function getStatusList()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
        ];
    }
}

if (!function_exists('getKondisiList')) {
    /**
     * Get list kondisi barang
     */
    function getKondisiList()
    {
        return [
            'baik' => 'Baik',
            'rusak' => 'Rusak',
            'diperbaiki' => 'Diperbaiki',
        ];
    }
}