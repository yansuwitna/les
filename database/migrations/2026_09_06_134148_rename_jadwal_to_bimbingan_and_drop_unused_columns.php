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
        if (Schema::hasTable('jadwal')) {
            Schema::table('jadwal', function (Blueprint $table) {
                if (Schema::hasColumn('jadwal', 'materi_id')) {
                    try {
                        $table->dropForeign(['materi_id']);
                    } catch (\Exception $e) {
                        // ignore if not foreign key
                    }
                    $table->dropColumn('materi_id');
                }
                if (Schema::hasColumn('jadwal', 'keterangan')) {
                    $table->dropColumn('keterangan');
                }
            });

            Schema::rename('jadwal', 'bimbingan');
        } elseif (Schema::hasTable('bimbingan')) {
            Schema::table('bimbingan', function (Blueprint $table) {
                if (Schema::hasColumn('bimbingan', 'materi_id')) {
                    try {
                        $table->dropForeign(['materi_id']);
                    } catch (\Exception $e) {
                        // ignore
                    }
                    $table->dropColumn('materi_id');
                }
                if (Schema::hasColumn('bimbingan', 'keterangan')) {
                    $table->dropColumn('keterangan');
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
                if (!Schema::hasColumn('bimbingan', 'materi_id')) {
                    $table->foreignId('materi_id')->nullable()->constrained('materi')->nullOnDelete();
                }
                if (!Schema::hasColumn('bimbingan', 'keterangan')) {
                    $table->text('keterangan')->nullable();
                }
            });

            Schema::rename('bimbingan', 'jadwal');
        }
    }
};
