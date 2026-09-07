<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('jadwal')) {
            Schema::create('jadwal', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bimbingan_id')->nullable()->constrained('bimbingan')->cascadeOnDelete();
                $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
                $table->foreignId('guru_id')->nullable()->constrained('guru')->cascadeOnDelete();
                $table->string('hari');
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->string('keterangan')->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });

            // Migrasi data jadwal yang sudah ada dari tabel bimbingan jika ada
            if (Schema::hasTable('bimbingan') && Schema::hasColumn('bimbingan', 'hari') && Schema::hasColumn('bimbingan', 'jam_mulai')) {
                try {
                    $existingJadwal = DB::table('bimbingan')
                        ->whereNotNull('hari')
                        ->whereNotNull('jam_mulai')
                        ->whereNotNull('jam_selesai')
                        ->get();

                    foreach ($existingJadwal as $item) {
                        DB::table('jadwal')->insert([
                            'bimbingan_id' => $item->id,
                            'siswa_id' => $item->siswa_id,
                            'guru_id' => $item->guru_id,
                            'hari' => $item->hari,
                            'jam_mulai' => $item->jam_mulai,
                            'jam_selesai' => $item->jam_selesai,
                            'keterangan' => null,
                            'aktif' => $item->aktif ?? true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } catch (\Exception $e) {
                    // Abaikan jika data tidak dapat disalin otomatis
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
