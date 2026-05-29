<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id')->unique();
            $table->unsignedBigInteger('kasir_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('nama_pelanggan')->nullable();
            $table->string('loyalty_id')->nullable();
            $table->enum('metode_bayar', ['cash', 'qris', 'debit', 'kredit'])->default('cash');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('nominal_bayar', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('status', ['draft', 'sukses', 'tertahan', 'mencurigakan', 'ditolak'])->default('draft');
            $table->string('fraud_reason')->nullable();
            $table->string('fraud_id')->nullable();
            $table->boolean('admin_reviewed')->default(false);
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('terminal_id')->nullable();
            $table->json('metadata')->nullable(); // data tambahan
            $table->timestamps();

            $table->foreign('kasir_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
