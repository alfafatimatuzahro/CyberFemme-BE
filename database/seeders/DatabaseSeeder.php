<?php

namespace Database\Seeders;

use App\Models\FraudRule;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === ADMIN (Pemilik UMKM) ===
        $admin = User::create([
            'username' => 'alneyra_admin',
            'email' => 'admin@alneyra.id',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
            'nama_umkm' => 'Alneyra Coffe',
            'alamat_umkm' => 'Jl. Sudirman No.123, Jakarta Selatan',
            'security_question' => 'Nama Lokasi UMKM?',
            'security_answer' => Hash::make('alneyra'),
            'is_active' => true,
        ]);

        // Buat fraud rules default untuk admin ini
        FraudRule::create([
            'admin_id' => $admin->id,
            'batas_nominal_max' => 5000000,
            'batas_nominal_aktif' => true,
            'batas_qty_max' => 20,
            'batas_qty_aktif' => true,
            'rentang_duplikasi_menit' => 5,
            'anti_spam_aktif' => true,
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'jam_operasional_aktif' => true,
            'auto_logout_aktif' => true,
        ]);

        // === KASIR ===
        $kasirAnnisa = User::create([
            'username' => 'annisa',
            'email' => 'annisa@alneyra.id',
            'password' => Hash::make('Kasir123!'),
            'role' => 'kasir',
            'admin_id' => $admin->id,
            'security_question' => 'Nama Hewan Peliharaan?',
            'security_answer' => Hash::make('kucing'),
            'is_active' => true,
        ]);

        $kasirBudi = User::create([
            'username' => 'kasir_budiono',
            'email' => 'budiono@alneyra.id',
            'password' => Hash::make('Kasir123!'),
            'role' => 'kasir',
            'admin_id' => $admin->id,
            'security_question' => 'Kota Kelahiran Anda?',
            'security_answer' => Hash::make('surabaya'),
            'is_active' => false, // nonaktif
        ]);

        $kasirCitra = User::create([
            'username' => 'staf_citra',
            'email' => 'citra@alneyra.id',
            'password' => Hash::make('Kasir123!'),
            'role' => 'kasir',
            'admin_id' => $admin->id,
            'security_question' => 'Nama Hewan Peliharaan?',
            'security_answer' => Hash::make('anjing'),
            'is_active' => true,
        ]);

        // === PRODUK ===
        $products = [
            ['nama' => 'Chocolate Cake', 'sku' => 'DSS3000', 'kategori' => 'Dessert', 'harga' => 15000, 'stok' => 50],
            ['nama' => 'Brown Sugar Latte', 'sku' => 'COF4001', 'kategori' => 'Coffee', 'harga' => 20000, 'stok' => 100],
            ['nama' => 'Caramel Macchiato', 'sku' => 'COF4002', 'kategori' => 'Coffee', 'harga' => 25000, 'stok' => 80],
            ['nama' => 'Matcha Latte', 'sku' => 'COF4003', 'kategori' => 'Coffee', 'harga' => 22000, 'stok' => 60],
            ['nama' => 'Croissant', 'sku' => 'FOD5001', 'kategori' => 'Food', 'harga' => 18000, 'stok' => 30],
            ['nama' => 'Club Sandwich', 'sku' => 'FOD5002', 'kategori' => 'Food', 'harga' => 35000, 'stok' => 25],
            ['nama' => 'Cheese Stick', 'sku' => 'SNK6001', 'kategori' => 'Snack', 'harga' => 12000, 'stok' => 200],
            ['nama' => 'Potato Wedges', 'sku' => 'SNK6002', 'kategori' => 'Snack', 'harga' => 15000, 'stok' => 150],
            ['nama' => 'Tiramisu', 'sku' => 'DSS3001', 'kategori' => 'Dessert', 'harga' => 28000, 'stok' => 20],
        ];

        foreach ($products as $p) {
            Product::create(array_merge($p, ['admin_id' => $admin->id]));
        }
        // === TRANSAKSI DUMMY ===

Transaction::create([
    'invoice_id' => 'INV001',
    'kasir_id' => $kasirAnnisa->id,
    'admin_id' => $admin->id,
    'nama_pelanggan' => 'Budi',
    'metode_bayar' => 'cash',
    'subtotal' => 100000,
    'ppn' => 11000,
    'diskon' => 0,
    'total' => 111000,
    'nominal_bayar' => 120000,
    'kembalian' => 9000,
    'status' => 'sukses',
]);

Transaction::create([
    'invoice_id' => 'INV002',
    'kasir_id' => $kasirAnnisa->id,
    'admin_id' => $admin->id,
    'nama_pelanggan' => 'Andi',
    'metode_bayar' => 'qris',
    'subtotal' => 250000,
    'ppn' => 27500,
    'diskon' => 0,
    'total' => 277500,
    'nominal_bayar' => 277500,
    'kembalian' => 0,
    'status' => 'sukses',
]);

Transaction::create([
    'invoice_id' => 'INV003',
    'kasir_id' => $kasirCitra->id,
    'admin_id' => $admin->id,
    'nama_pelanggan' => 'Siti',
    'metode_bayar' => 'cash',
    'subtotal' => 5000000,
    'ppn' => 550000,
    'diskon' => 0,
    'total' => 5550000,
    'nominal_bayar' => 5550000,
    'kembalian' => 0,
    'status' => 'mencurigakan',
    'fraud_reason' => 'Nominal transaksi terlalu besar'
]);


        $this->command->info('✅ Seeder selesai! Data berhasil dibuat.');
        $this->command->info('');
        $this->command->info('=== CREDENTIALS ===');
        $this->command->info('ADMIN  - Nama UMKM: "Alneyra Coffe" | Password: Admin123!');
        $this->command->info('KASIR  - Username: annisa | Password: Kasir123!');
        $this->command->info('KASIR  - Username: staf_citra | Password: Kasir123!');
    }
}
