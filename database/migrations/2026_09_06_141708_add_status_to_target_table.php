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
        Schema::table('target', function (Blueprint $table) {
            if (!Schema::hasColumn('target', 'status')) {
                $table->enum('status', ['aktif', 'selesai'])->default('aktif')->after('deskripsi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target', function (Blueprint $table) {
            if (Schema::hasColumn('target', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
