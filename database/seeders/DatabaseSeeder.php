<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Pengaturan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        Admin::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'email' => 'admin@les.com',
                'kata_sandi' => Hash::make('admin'),
            ]
        );

        // Guru
        Guru::firstOrCreate(
            ['nik' => '1234567890123456'],
            [
                'nama' => 'Guru Pertama',
                'nip' => '198501012010011001',
                'email' => 'guru@les.com',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Guru No. 1',
                'kata_sandi' => Hash::make('password'),
            ]
        );

        // Siswa
        Siswa::firstOrCreate(
            ['nomor_siswa' => '2026000001'],
            [
                'nama' => 'Budi Santoso',
                'nis' => '1001',
                'nama_wali' => 'Bapak Santoso',
                'no_hp' => '081298765432',
                'alamat' => 'Jl. Siswa No. 1',
                'kata_sandi' => Hash::make('password'),
            ]
        );

        // Pengaturan
        if (!Pengaturan::first()) {
            Pengaturan::create([
                'nama_les' => 'Les Ceria',
                'alamat_les' => 'Jl. Pendidikan No. 1, Kota Belajar',
                'kontak_les' => '0812-3456-7890'
            ]);
        }
    }
}
