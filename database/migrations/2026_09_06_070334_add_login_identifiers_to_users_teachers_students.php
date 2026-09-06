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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('email')->nullable()->change();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('nik', 30)->nullable()->after('user_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('student_number', 10)->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('student_number');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('nik');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
};
