<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'borrowed', 'returned'])->default('pending');
            $table->dateTime('tgl_pinjam');
            $table->dateTime('tgl_estimasi_kembali');
            $table->dateTime('tgl_kembali')->nullable();
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('no_transaksi');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('peminjaman_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->foreignId('inventaris_id')->constrained('inventaris')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('kondisi_sebelum')->default('baik');
            $table->string('kondisi_sesudah')->nullable();
            $table->text('catatan_item')->nullable();
            $table->timestamps();
            
            $table->index(['peminjaman_id', 'inventaris_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_items');
        Schema::dropIfExists('peminjaman');
    }
};