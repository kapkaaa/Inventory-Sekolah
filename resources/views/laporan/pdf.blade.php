<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman Inventaris</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #f3f4f6;
            color: #333;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-returned { background-color: #e5e7eb; color: #1f2937; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .total {
            margin-top: 20px;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMINJAMAN INVENTARIS</h1>
        <p>Sistem Informasi Inventaris Sekolah</p>
        <p>Tanggal Cetak: {{ date('d F Y H:i') }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="30%"><strong>Periode</strong></td>
                <td>: {{ request('start_date') ? date('d F Y', strtotime(request('start_date'))) : 'Semua' }} 
                   s/d 
                   {{ request('end_date') ? date('d F Y', strtotime(request('end_date'))) : 'Sekarang' }}
                </td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>: {{ request('status') ? strtoupper(request('status')) : 'Semua Status' }}</td>
            </tr>
            <tr>
                <td><strong>Total Data</strong></td>
                <td>: {{ $peminjaman->count() }} transaksi</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. Transaksi</th>
                <th width="20%">Peminjam</th>
                <th width="15%">Tgl Pinjam</th>
                <th width="15%">Tgl Kembali</th>
                <th width="10%">Status</th>
                <th width="10%">Items</th>
                <th width="10%">Denda</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDenda = 0; @endphp
            @foreach($peminjaman as $index => $p)
            @php $totalDenda += $p->total_denda; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->no_transaksi }}</td>
                <td>{{ $p->user->name }}</td>
                <td>{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                <td>{{ $p->tgl_kembali ? $p->tgl_kembali->format('d/m/Y') : '-' }}</td>
                <td>
                    <span class="status status-{{ $p->status }}">
                        {{ strtoupper($p->status) }}
                    </span>
                </td>
                <td style="text-align: center">{{ $p->items->count() }}</td>
                <td style="text-align: right">{{ $p->total_denda > 0 ? number_format($p->total_denda, 0, ',', '.') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold;">TOTAL DENDA:</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }}</p>
        <p>{{ date('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>