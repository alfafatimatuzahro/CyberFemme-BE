<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            // Rule 1: Batas maksimal nominal transaksi
            $table->decimal('batas_nominal_max', 15, 2)->default(5000000);
            $table->boolean('batas_nominal_aktif')->default(true);
            // Rule 2: Batas maksimal qty per item
            $table->integer('batas_qty_max')->default(20);
            $table->boolean('batas_qty_aktif')->default(true);
            // Rule 3: Anti-spam transaksi ganda
            $table->integer('rentang_duplikasi_menit')->default(5);
            $table->boolean('anti_spam_aktif')->default(true);
            // Rule 4: Jam operasional
            $table->time('jam_buka')->default('08:00:00');
            $table->time('jam_tutup')->default('22:00:00');
            $table->boolean('jam_operasional_aktif')->default(true);
            $table->boolean('auto_logout_aktif')->default(true);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_rules');
    }
};
