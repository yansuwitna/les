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
        Schema::table('materi', function (Blueprint $table) {
            if (!Schema::hasColumn('materi', 'guru_id')) {
                $table->foreignId('guru_id')->nullable()->after('id')->constrained('guru')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('materi', 'siswa_id')) {
                $table->foreignId('siswa_id')->nullable()->after('guru_id')->constrained('siswa')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            if (Schema::hasColumn('materi', 'siswa_id')) {
                $table->dropForeign(['siswa_id']);
                $table->dropColumn('siswa_id');
            }
            if (Schema::hasColumn('materi', 'guru_id')) {
                $table->dropForeign(['guru_id']);
                $table->dropColumn('guru_id');
            }
        });
    }
};
