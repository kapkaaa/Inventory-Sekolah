<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        // Create Regular Users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '081234567891',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '081234567892',
        ]);

        // Create Sample Inventaris
        $kategoriList = ['Elektronik', 'Olahraga', 'Alat Tulis', 'Multimedia', 'Lab'];
        $items = [
            ['nama' => 'Laptop Lenovo ThinkPad', 'kategori' => 'Elektronik', 'jumlah' => 10],
            ['nama' => 'Proyektor Epson', 'kategori' => 'Elektronik', 'jumlah' => 5],
            ['nama' => 'Kamera Canon DSLR', 'kategori' => 'Multimedia', 'jumlah' => 3],
            ['nama' => 'Bola Basket Molten', 'kategori' => 'Olahraga', 'jumlah' => 15],
            ['nama' => 'Raket Badminton Yonex', 'kategori' => 'Olahraga', 'jumlah' => 20],
            ['nama' => 'Mikroskop Olympus', 'kategori' => 'Lab', 'jumlah' => 8],
            ['nama' => 'Printer HP LaserJet', 'kategori' => 'Elektronik', 'jumlah' => 6],
            ['nama' => 'Whiteboard 120x180cm', 'kategori' => 'Alat Tulis', 'jumlah' => 12],
            ['nama' => 'Speaker Portable JBL', 'kategori' => 'Elektronik', 'jumlah' => 8],
            ['nama' => 'Tripod Manfrotto', 'kategori' => 'Multimedia', 'jumlah' => 5],
        ];

        foreach ($items as $item) {
            $kode = 'INV-' . strtoupper(Str::random(8));
            
            Inventaris::create([
                'kode_barang' => $kode,
                'nama_barang' => $item['nama'],
                'kategori' => $item['kategori'],
                'deskripsi' => 'Inventaris sekolah untuk kegiatan pembelajaran dan ekstrakurikuler',
                'jumlah_total' => $item['jumlah'],
                'jumlah_tersedia' => $item['jumlah'],
                'kondisi' => 'baik',
                'lokasi' => 'Ruang ' . $item['kategori'],
                'qr_code' => 'qrcodes/' . $kode . '.png',
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Login: admin@sekolah.com / password');
        $this->command->info('User Login: john@sekolah.com / password');
    }
}