<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'kasir'])->default('kasir');
            $table->string('nama_umkm')->nullable(); // untuk admin
            $table->string('alamat_umkm')->nullable(); // untuk admin
            $table->unsignedBigInteger('admin_id')->nullable(); // kasir belongs to admin
            $table->string('profile_photo')->nullable();
            $table->string('security_question')->nullable();
            $table->string('security_answer')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('temp_password')->nullable(); // password sementara dari admin
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
