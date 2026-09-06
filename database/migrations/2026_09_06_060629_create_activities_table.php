<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->integer('sequence_number'); // nomor urut
            $table->text('description'); // keterangan
            $table->text('method')->nullable(); // Tata Cara/Teknik
            $table->date('start_date'); // tanggal pelaksanaan
            $table->date('end_date')->nullable(); // tanggal selesai
            $table->time('start_time'); // Waktu Mulai
            $table->time('end_time')->nullable(); // Waktu Selesai
            $table->text('result_note')->nullable(); // Catatan Hasil
            $table->text('next_activity')->nullable(); // Kegiatan Selanjutnya
            $table->enum('status', ['Diulangi', 'Lanjut', 'Selesai'])->default('Lanjut'); // Status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
