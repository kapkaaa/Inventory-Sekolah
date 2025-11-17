@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                {{ isset($inventaris) ? 'Edit Inventaris' : 'Tambah Inventaris' }}
            </h1>
            <a href="{{ route('inventaris.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Kembali
            </a>
        </div>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ isset($inventaris) ? route('inventaris.update', $inventaris->id) : route('inventaris.store') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="bg-white rounded-lg shadow-md p-6">
            @csrf
            @if(isset($inventaris))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Barang -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_barang" 
                           value="{{ old('nama_barang', $inventaris->nama_barang ?? '') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Laptop Lenovo ThinkPad">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="kategori" 
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="Elektronik" {{ old('kategori', $inventaris->kategori ?? '') == 'Elektronik' ? 'selected' : '' }}>Elektronik</option>
                        <option value="Olahraga" {{ old('kategori', $inventaris->kategori ?? '') == 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                        <option value="Alat Tulis" {{ old('kategori', $inventaris->kategori ?? '') == 'Alat Tulis' ? 'selected' : '' }}>Alat Tulis</option>
                        <option value="Multimedia" {{ old('kategori', $inventaris->kategori ?? '') == 'Multimedia' ? 'selected' : '' }}>Multimedia</option>
                        <option value="Lab" {{ old('kategori', $inventaris->kategori ?? '') == 'Lab' ? 'selected' : '' }}>Lab</option>
                        <option value="Furnitur" {{ old('kategori', $inventaris->kategori ?? '') == 'Furnitur' ? 'selected' : '' }}>Furnitur</option>
                        <option value="Lainnya" {{ old('kategori', $inventaris->kategori ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Jumlah Total -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Total <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="jumlah_total" 
                           value="{{ old('jumlah_total', $inventaris->jumlah_total ?? 1) }}"
                           min="0"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>

                <!-- Kondisi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kondisi <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi" 
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="baik" {{ old('kondisi', $inventaris->kondisi ?? 'baik') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ old('kondisi', $inventaris->kondisi ?? '') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="diperbaiki" {{ old('kondisi', $inventaris->kondisi ?? '') == 'diperbaiki' ? 'selected' : '' }}>Diperbaiki</option>
                    </select>
                </div>

                <!-- Lokasi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi
                    </label>
                    <input type="text" 
                           name="lokasi" 
                           value="{{ old('lokasi', $inventaris->lokasi ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Ruang Lab Komputer">
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Deskripsi detail barang...">{{ old('deskripsi', $inventaris->deskripsi ?? '') }}</textarea>
                </div>

                <!-- Foto -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Barang
                    </label>
                    <input type="file" 
                           name="foto" 
                           accept="image/jpeg,image/png,image/jpg"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB</p>
                    
                    @if(isset($inventaris) && $inventaris->foto)
                    <div class="mt-3">
                        <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                        <img src="{{ $inventaris->foto_url }}" alt="Foto barang" class="w-32 h-32 object-cover rounded-lg border">
                    </div>
                    @endif
                </div>
            </div>

            @if(isset($inventaris))
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-gray-700"><strong>Kode Barang:</strong> {{ $inventaris->kode_barang }}</p>
                <p class="text-sm text-gray-700 mt-1"><strong>Stok Tersedia:</strong> {{ $inventaris->jumlah_tersedia }} dari {{ $inventaris->jumlah_total }}</p>
                @if($inventaris->qr_code)
                <div class="mt-3">
                    <p class="text-sm text-gray-700 font-semibold mb-2">QR Code:</p>
                    <img src="{{ $inventaris->qr_code_url }}" alt="QR Code" class="w-32 h-32 border rounded">
                </div>
                @endif
            </div>
            @endif

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('inventaris.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    {{ isset($inventaris) ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection