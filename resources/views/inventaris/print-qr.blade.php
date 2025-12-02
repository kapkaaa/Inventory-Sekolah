<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code - {{ $inventaris->kode_barang }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 20mm;
            }
            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: #f3f4f6;
        }

        .qr-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }

        .header {
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1f2937;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .qr-code {
            margin: 30px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 10px;
            border: 3px dashed #3b82f6;
        }

        .qr-code img {
            max-width: 300px;
            width: 100%;
            height: auto;
        }

        .info {
            margin-top: 20px;
            padding: 20px;
            background: #eff6ff;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dbeafe;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #1f2937;
        }

        .info-value {
            color: #4b5563;
            text-align: right;
        }

        .kode-barang {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
            margin: 10px 0;
            letter-spacing: 2px;
        }

        .instructions {
            margin-top: 20px;
            padding: 15px;
            background: #fef3c7;
            border-radius: 10px;
            font-size: 13px;
            color: #78350f;
            border-left: 4px solid #f59e0b;
        }

        .btn-print {
            margin-top: 30px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-print:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="header">
            <h1>📦 QR Code Inventaris</h1>
            <p>Sistem Inventaris Sekolah</p>
        </div>

        <div class="kode-barang">{{ $inventaris->kode_barang }}</div>

        <div class="qr-code">
            @if($inventaris->qr_code && Storage::disk('public')->exists($inventaris->qr_code))
                <img src="{{ asset('storage/' . $inventaris->qr_code) }}" alt="QR Code {{ $inventaris->kode_barang }}">
            @else
                <p style="color: #ef4444;">QR Code tidak ditemukan</p>
            @endif
        </div>

        <div class="info">
            <div class="info-row">
                <span class="info-label">Nama Barang:</span>
                <span class="info-value">{{ $inventaris->nama_barang }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kategori:</span>
                <span class="info-value">{{ $inventaris->kategori }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lokasi:</span>
                <span class="info-value">{{ $inventaris->lokasi ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Stok Total:</span>
                <span class="info-value">{{ $inventaris->jumlah_total }}</span>
            </div>
        </div>

        <div class="instructions">
            <strong>📱 Cara Menggunakan:</strong><br>
            Scan QR Code ini dengan kamera HP untuk langsung mengajukan peminjaman barang ini.
        </div>

        <div class="footer no-print">
            <p>© {{ date('Y') }} Sistem Inventaris Sekolah</p>
            <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
        </div>
    </div>
</body>
</html>