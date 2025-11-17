<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('kategori')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('jumlah_total')->default(0);
            $table->integer('jumlah_tersedia')->default(0);
            $table->enum('kondisi', ['baik', 'rusak', 'diperbaiki'])->default('baik');
            $table->string('lokasi')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
            
            $table->index('kode_barang');
            $table->index('kategori');
            $table->index('kondisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};