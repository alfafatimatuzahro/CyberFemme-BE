<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id'); // siapa yang dapat notif
            $table->enum('type', ['info', 'urgent', 'warning'])->default('info');
            $table->string('judul');
            $table->text('pesan');
            $table->string('ref_type')->nullable(); // 'transaction', 'login_log'
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_notifications');
    }
};
