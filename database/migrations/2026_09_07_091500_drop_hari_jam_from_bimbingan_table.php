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
        if (Schema::hasTable('bimbingan')) {
            Schema::table('bimbingan', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('bimbingan', 'hari')) {
                    $columnsToDrop[] = 'hari';
                }
                if (Schema::hasColumn('bimbingan', 'jam_mulai')) {
                    $columnsToDrop[] = 'jam_mulai';
                }
                if (Schema::hasColumn('bimbingan', 'jam_selesai')) {
                    $columnsToDrop[] = 'jam_selesai';
                }

                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bimbingan')) {
            Schema::table('bimbingan', function (Blueprint $table) {
                if (!Schema::hasColumn('bimbingan', 'hari')) {
                    $table->string('hari')->nullable()->after('siswa_id');
                }
                if (!Schema::hasColumn('bimbingan', 'jam_mulai')) {
                    $table->time('jam_mulai')->nullable()->after('hari');
                }
                if (!Schema::hasColumn('bimbingan', 'jam_selesai')) {
                    $table->time('jam_selesai')->nullable()->after('jam_mulai');
                }
            });
        }
    }
};
