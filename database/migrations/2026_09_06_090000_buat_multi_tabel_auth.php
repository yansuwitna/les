<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Tambah tabel admin
        if (!Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('username')->unique();
                $table->string('email')->nullable();
                $table->string('kata_sandi');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Tambah kolom kredensial login pada tabel guru
        if (Schema::hasTable('guru')) {
            Schema::table('guru', function (Blueprint $table) {
                if (!Schema::hasColumn('guru', 'nama')) {
                    $table->string('nama')->after('id')->nullable();
                }
                if (!Schema::hasColumn('guru', 'kata_sandi')) {
                    $table->string('kata_sandi')->after('nik')->nullable();
                }
                if (!Schema::hasColumn('guru', 'email')) {
                    $table->string('email')->after('nip')->nullable();
                }
                if (!Schema::hasColumn('guru', 'remember_token')) {
                    $table->rememberToken();
                }
            });
        }

        // 3. Tambah kolom kredensial login pada tabel siswa
        if (Schema::hasTable('siswa')) {
            Schema::table('siswa', function (Blueprint $table) {
                if (!Schema::hasColumn('siswa', 'kata_sandi')) {
                    $table->string('kata_sandi')->after('nomor_siswa')->nullable();
                }
                if (!Schema::hasColumn('siswa', 'nama_wali')) {
                    $table->string('nama_wali')->after('nama')->nullable();
                }
                if (!Schema::hasColumn('siswa', 'remember_token')) {
                    $table->rememberToken();
                }
            });
        }

        // 4. Migrasi data lama dari tabel pengguna ke tabel masing-masing
        if (Schema::hasTable('pengguna')) {
            // Pindahkan Admin
            $admins = DB::table('pengguna')->where('peran', 'admin')->get();
            foreach ($admins as $adm) {
                DB::table('admin')->updateOrInsert(
                    ['username' => $adm->username],
                    [
                        'nama' => $adm->nama,
                        'email' => $adm->email,
                        'kata_sandi' => $adm->kata_sandi,
                        'created_at' => $adm->created_at,
                        'updated_at' => $adm->updated_at,
                    ]
                );
            }

            // Pindahkan kredensial guru dari pengguna ke tabel guru
            if (Schema::hasColumn('guru', 'pengguna_id')) {
                $gurus = DB::table('guru')->get();
                foreach ($gurus as $g) {
                    if (!empty($g->pengguna_id)) {
                        $p = DB::table('pengguna')->where('id', $g->pengguna_id)->first();
                        if ($p) {
                            DB::table('guru')->where('id', $g->id)->update([
                                'nama' => $g->nama ?: $p->nama,
                                'kata_sandi' => $g->kata_sandi ?: $p->kata_sandi,
                                'email' => $g->email ?: $p->email,
                            ]);
                        }
                    }
                }
            }

            // Pindahkan kredensial siswa/ortu dari pengguna ke tabel siswa
            if (Schema::hasColumn('siswa', 'pengguna_id')) {
                $siswas = DB::table('siswa')->get();
                foreach ($siswas as $s) {
                    if (!empty($s->pengguna_id)) {
                        $p = DB::table('pengguna')->where('id', $s->pengguna_id)->first();
                        if ($p) {
                            DB::table('siswa')->where('id', $s->id)->update([
                                'nama_wali' => $s->nama_wali ?: $p->nama,
                                'kata_sandi' => $s->kata_sandi ?: $p->kata_sandi,
                            ]);
                        }
                    }
                }
            }

            // Pastikan tidak ada kata_sandi yang kosong di guru & siswa
            DB::table('guru')->whereNull('kata_sandi')->update([
                'kata_sandi' => Hash::make('password')
            ]);
            DB::table('guru')->whereNull('nama')->update([
                'nama' => 'Guru Pengajar'
            ]);

            DB::table('siswa')->whereNull('kata_sandi')->update([
                'kata_sandi' => Hash::make('password')
            ]);

            // Drop foreign key constraints explicitly before dropping column
            try {
                DB::statement('ALTER TABLE guru DROP FOREIGN KEY teachers_user_id_foreign');
            } catch (\Throwable $e) {}
            try {
                DB::statement('ALTER TABLE guru DROP FOREIGN KEY guru_pengguna_id_foreign');
            } catch (\Throwable $e) {}

            try {
                DB::statement('ALTER TABLE siswa DROP FOREIGN KEY students_ortu_id_foreign');
            } catch (\Throwable $e) {}
            try {
                DB::statement('ALTER TABLE siswa DROP FOREIGN KEY siswa_pengguna_id_foreign');
            } catch (\Throwable $e) {}

            // 5. Hapus kolom pengguna_id dari guru dan siswa
            if (Schema::hasColumn('guru', 'pengguna_id')) {
                Schema::table('guru', function (Blueprint $table) {
                    $table->dropColumn('pengguna_id');
                });
            }

            if (Schema::hasColumn('siswa', 'pengguna_id')) {
                Schema::table('siswa', function (Blueprint $table) {
                    $table->dropColumn('pengguna_id');
                });
            }

            // 6. HILANGKAN TABEL PENGGUNA sesuai permintaan user
            Schema::dropIfExists('pengguna');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
