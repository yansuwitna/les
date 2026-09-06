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
        // 1. Kolom status aktif pada tabel guru
        if (Schema::hasTable('guru') && !Schema::hasColumn('guru', 'aktif')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->boolean('aktif')->default(true)->after('foto');
            });
        }

        // 2. Kolom status aktif pada tabel siswa
        if (Schema::hasTable('siswa') && !Schema::hasColumn('siswa', 'aktif')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->boolean('aktif')->default(true)->after('foto');
            });
        }

        // 3. Kolom pendukung pada tabel jadwal
        if (Schema::hasTable('jadwal')) {
            Schema::table('jadwal', function (Blueprint $table) {
                if (!Schema::hasColumn('jadwal', 'materi_id')) {
                    $table->foreignId('materi_id')->nullable()->after('guru_id')->constrained('materi')->nullOnDelete();
                }
                if (!Schema::hasColumn('jadwal', 'keterangan')) {
                    $table->string('keterangan')->nullable()->after('jam_selesai');
                }
                if (!Schema::hasColumn('jadwal', 'aktif')) {
                    $table->boolean('aktif')->default(true)->after('jam_selesai');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('guru') && Schema::hasColumn('guru', 'aktif')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->dropColumn('aktif');
            });
        }

        if (Schema::hasTable('siswa') && Schema::hasColumn('siswa', 'aktif')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('aktif');
            });
        }

        if (Schema::hasTable('jadwal')) {
            Schema::table('jadwal', function (Blueprint $table) {
                if (Schema::hasColumn('jadwal', 'materi_id')) {
                    $table->dropForeign(['materi_id']);
                    $table->dropColumn('materi_id');
                }
                if (Schema::hasColumn('jadwal', 'keterangan')) {
                    $table->dropColumn('keterangan');
                }
                if (Schema::hasColumn('jadwal', 'aktif')) {
                    $table->dropColumn('aktif');
                }
            });
        }
    }
};
