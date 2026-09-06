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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. users -> pengguna
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'name')) {
                Schema::table('users', fn (Blueprint $table) => $table->renameColumn('name', 'nama'));
            }
            if (Schema::hasColumn('users', 'password')) {
                Schema::table('users', fn (Blueprint $table) => $table->renameColumn('password', 'kata_sandi'));
            }
            if (Schema::hasColumn('users', 'role')) {
                DB::statement("ALTER TABLE users CHANGE COLUMN role peran ENUM('admin', 'guru', 'ortu') NOT NULL DEFAULT 'ortu'");
            }
            Schema::rename('users', 'pengguna');
        }

        // 2. teachers -> guru
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                if (Schema::hasColumn('teachers', 'user_id')) {
                    $table->renameColumn('user_id', 'pengguna_id');
                }
                if (Schema::hasColumn('teachers', 'phone')) {
                    $table->renameColumn('phone', 'no_hp');
                }
                if (Schema::hasColumn('teachers', 'address')) {
                    $table->renameColumn('address', 'alamat');
                }
                if (Schema::hasColumn('teachers', 'photo')) {
                    $table->renameColumn('photo', 'foto');
                }
            });
            Schema::rename('teachers', 'guru');
        }

        // 3. students -> siswa
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'student_number')) {
                    $table->renameColumn('student_number', 'nomor_siswa');
                }
                if (Schema::hasColumn('students', 'ortu_id')) {
                    $table->renameColumn('ortu_id', 'pengguna_id');
                }
                if (Schema::hasColumn('students', 'name')) {
                    $table->renameColumn('name', 'nama');
                }
                if (Schema::hasColumn('students', 'phone')) {
                    $table->renameColumn('phone', 'no_hp');
                }
                if (Schema::hasColumn('students', 'address')) {
                    $table->renameColumn('address', 'alamat');
                }
                if (Schema::hasColumn('students', 'photo')) {
                    $table->renameColumn('photo', 'foto');
                }
            });
            Schema::rename('students', 'siswa');
        }

        // 4. schedules -> jadwal
        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                if (Schema::hasColumn('schedules', 'student_id')) {
                    $table->renameColumn('student_id', 'siswa_id');
                }
                if (Schema::hasColumn('schedules', 'teacher_id')) {
                    $table->renameColumn('teacher_id', 'guru_id');
                }
                if (Schema::hasColumn('schedules', 'day')) {
                    $table->renameColumn('day', 'hari');
                }
                if (Schema::hasColumn('schedules', 'start_time')) {
                    $table->renameColumn('start_time', 'jam_mulai');
                }
                if (Schema::hasColumn('schedules', 'end_time')) {
                    $table->renameColumn('end_time', 'jam_selesai');
                }
            });
            Schema::rename('schedules', 'jadwal');
        }

        // 5. materials -> materi
        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                if (Schema::hasColumn('materials', 'name')) {
                    $table->renameColumn('name', 'nama');
                }
                if (Schema::hasColumn('materials', 'description')) {
                    $table->renameColumn('description', 'deskripsi');
                }
            });
            Schema::rename('materials', 'materi');
        }

        // 6. targets -> target
        if (Schema::hasTable('targets')) {
            Schema::table('targets', function (Blueprint $table) {
                if (Schema::hasColumn('targets', 'teacher_id')) {
                    $table->renameColumn('teacher_id', 'guru_id');
                }
                if (Schema::hasColumn('targets', 'student_id')) {
                    $table->renameColumn('student_id', 'siswa_id');
                }
                if (Schema::hasColumn('targets', 'material_id')) {
                    $table->renameColumn('material_id', 'materi_id');
                }
                if (Schema::hasColumn('targets', 'name')) {
                    $table->renameColumn('name', 'nama');
                }
                if (Schema::hasColumn('targets', 'description')) {
                    $table->renameColumn('description', 'deskripsi');
                }
            });
            Schema::rename('targets', 'target');
        }

        // 7. certificates -> sertifikat
        if (Schema::hasTable('certificates')) {
            Schema::table('certificates', function (Blueprint $table) {
                if (Schema::hasColumn('certificates', 'student_id')) {
                    $table->renameColumn('student_id', 'siswa_id');
                }
                if (Schema::hasColumn('certificates', 'material_id')) {
                    $table->renameColumn('material_id', 'materi_id');
                }
                if (Schema::hasColumn('certificates', 'file_path')) {
                    $table->renameColumn('file_path', 'file_sertifikat');
                }
                if (Schema::hasColumn('certificates', 'issue_date')) {
                    $table->renameColumn('issue_date', 'tanggal_terbit');
                }
            });
            Schema::rename('certificates', 'sertifikat');
        }

        // 8. stars -> bintang
        if (Schema::hasTable('stars')) {
            Schema::table('stars', function (Blueprint $table) {
                if (Schema::hasColumn('stars', 'student_id')) {
                    $table->renameColumn('student_id', 'siswa_id');
                }
                if (Schema::hasColumn('stars', 'target_id')) {
                    $table->renameColumn('target_id', 'target_id');
                }
                if (Schema::hasColumn('stars', 'stars_count')) {
                    $table->renameColumn('stars_count', 'jumlah_bintang');
                }
                if (Schema::hasColumn('stars', 'awarded_date')) {
                    $table->renameColumn('awarded_date', 'tanggal_diberikan');
                }
            });
            Schema::rename('stars', 'bintang');
        }

        // 9. activities -> kegiatan
        if (Schema::hasTable('activities')) {
            Schema::table('activities', function (Blueprint $table) {
                if (Schema::hasColumn('activities', 'sequence_number')) {
                    $table->renameColumn('sequence_number', 'nomor_urut');
                }
                if (Schema::hasColumn('activities', 'description')) {
                    $table->renameColumn('description', 'keterangan');
                }
                if (Schema::hasColumn('activities', 'method')) {
                    $table->renameColumn('method', 'metode');
                }
                if (Schema::hasColumn('activities', 'start_date')) {
                    $table->renameColumn('start_date', 'tanggal_mulai');
                }
                if (Schema::hasColumn('activities', 'end_date')) {
                    $table->renameColumn('end_date', 'tanggal_selesai');
                }
                if (Schema::hasColumn('activities', 'start_time')) {
                    $table->renameColumn('start_time', 'waktu_mulai');
                }
                if (Schema::hasColumn('activities', 'end_time')) {
                    $table->renameColumn('end_time', 'waktu_selesai');
                }
                if (Schema::hasColumn('activities', 'result_note')) {
                    $table->renameColumn('result_note', 'catatan_hasil');
                }
                if (Schema::hasColumn('activities', 'next_activity')) {
                    $table->renameColumn('next_activity', 'kegiatan_selanjutnya');
                }
            });
            Schema::rename('activities', 'kegiatan');
        }

        // 10. settings -> pengaturan
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (Schema::hasColumn('settings', 'les_name')) {
                    $table->renameColumn('les_name', 'nama_les');
                }
                if (Schema::hasColumn('settings', 'les_address')) {
                    $table->renameColumn('les_address', 'alamat_les');
                }
                if (Schema::hasColumn('settings', 'les_contact')) {
                    $table->renameColumn('les_contact', 'kontak_les');
                }
                if (Schema::hasColumn('settings', 'les_logo')) {
                    $table->renameColumn('les_logo', 'logo_les');
                }
                if (Schema::hasColumn('settings', 'les_slide')) {
                    $table->renameColumn('les_slide', 'slide_les');
                }
            });
            Schema::rename('settings', 'pengaturan');
        }

        // 11. testimonials -> testimoni
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                if (Schema::hasColumn('testimonials', 'name')) {
                    $table->renameColumn('name', 'nama');
                }
                if (Schema::hasColumn('testimonials', 'content')) {
                    $table->renameColumn('content', 'pesan');
                }
                if (Schema::hasColumn('testimonials', 'photo')) {
                    $table->renameColumn('photo', 'foto');
                }
                if (Schema::hasColumn('testimonials', 'is_active')) {
                    $table->renameColumn('is_active', 'aktif');
                }
            });
            Schema::rename('testimonials', 'testimoni');
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
