# Rencana Pengembangan Sistem Informasi Bimbingan Belajar (Les)

## 1. Ringkasan Proyek
Proyek ini adalah pengembangan aplikasi berbasis web untuk manajemen kegiatan bimbingan belajar (les). Aplikasi ini bertujuan untuk memudahkan proses administrasi, pemantauan kegiatan belajar mengajar, serta pelaporan perkembangan siswa kepada orang tua secara transparan dan terstruktur.

## 2. Tech Stack
*   **Backend:** Laravel (PHP Framework)
*   **Frontend:** Vue.js
*   **Styling:** Tailwind CSS (dengan font 'Poppins')
*   **Database:** MySQL
*   **Desain UI/UX:** Gaya modern, bersih (clean), dan elegan.

## 3. Hak Akses (User Roles)
Aplikasi ini memiliki 3 peran pengguna utama:
1.  **Admin:** Pengelola utama sistem, mengatur master data, identitas les, dan penjadwalan.
2.  **Guru (Pengajar):** Pengelola kegiatan belajar mengajar, merencanakan materi, target, dan mencatat hasil kegiatan siswa.
3.  **Ortu (Orang Tua):** Pemantau perkembangan dan hasil belajar siswa berdasarkan catatan kegiatan dari guru.

## 4. Fitur Utama & Halaman

### A. Halaman Home (Landing Page)
*   Menampilkan profil Les (Nama, Logo, Alamat, Kontak).
*   Foto Slider/Galeri kegiatan.
*   Statistik jumlah pengajar dan siswa.
*   Jadwal kegiatan belajar.
*   Testimoni.

### B. Halaman Login
*   Autentikasi untuk Admin, Guru, dan Ortu.

### C. Dashboard Admin
*   **Manajemen Pengguna:** Mendaftarkan Guru, Siswa, dan Orang Tua.
*   **Manajemen Jadwal:** Memetakan jadwal belajar antara Siswa dan Guru pengajar.
*   **Pengaturan Sistem (Identitas Les):** Mengubah Nama, Alamat, Kontak, Logo, dan Foto Slide Home.
*   **Manajemen Konten:** Menambahkan/mengedit Testimoni untuk halaman Home.
*   **Galeri Siswa:** Menambahkan foto siswa.
*   **Sertifikat & Reward:**
    *   Mencetak Sertifikat Hasil Materi untuk siswa.
    *   Mencetak Bintang (Reward) dari setiap target kegiatan yang dicapai siswa.

### D. Dashboard Guru
*   **Pemetaan Materi:** Menentukan materi yang akan diterima oleh siswa.
*   **Penentuan Target:** Memberikan target pencapaian dari materi yang diberikan.
*   **Manajemen Kegiatan (Log Book):** Setiap target memiliki banyak kegiatan. Form kegiatan berisi:
    *   Nomor Urut
    *   Keterangan
    *   Tata Cara/Teknik
    *   Tanggal Pelaksanaan & Tanggal Selesai
    *   Waktu Mulai & Waktu Selesai
    *   Catatan Hasil
    *   Kegiatan Selanjutnya
    *   Status (Pilihan: Diulangi, Lanjut, Selesai)

### E. Dashboard Ortu (Orang Tua)
*   **Link Akses Khusus:** Diberikan tautan/akses login untuk memantau keadaan siswa.
*   **Pemantauan Progress:** Melihat jadwal anak, riwayat kegiatan, catatan hasil dari guru, target yang dicapai, bintang (reward), dan sertifikat.

## 5. Rancangan Struktur Database (Skema Awal)

*   `users`: Menyimpan data autentikasi (id, name, email, password, role: admin/guru/ortu).
*   `teachers`: Profil detail guru.
*   `students`: Profil siswa, terhubung dengan `ortu_id` (users) dan foto siswa.
*   `settings`: Menyimpan pengaturan profil les (nama, logo, kontak, alamat, banner).
*   `testimonials`: Menyimpan testimoni.
*   `schedules`: Relasi antara `student_id`, `teacher_id`, hari, jam mulai, jam selesai.
*   `materials`: Master data materi yang tersedia.
*   `targets`: Target spesifik dari suatu materi untuk seorang siswa, dikelola oleh guru.
*   `activities`: Log kegiatan harian berdasarkan target. Terdiri dari field (no_urut, keterangan, tata_cara, tgl_pelaksanaan, tgl_selesai, waktu_mulai, waktu_selesai, catatan_hasil, kegiatan_selanjutnya, status).
*   `certificates` & `stars`: Tabel pencatatan reward dan kelulusan materi siswa (bisa juga diintegrasikan dalam relasi target/materi).

## 6. Langkah-langkah Pengembangan (Milestone)
1.  **Tahap 1: Persiapan & Setup Proyek**
    *   Instalasi Laravel, Vue, Tailwind CSS.
    *   Konfigurasi database MySQL.
    *   Setup layout utama dan integrasi font Poppins.
2.  **Tahap 2: Database & Model (Backend)**
    *   Pembuatan Migration, Factory, dan Seeder.
    *   Pembuatan relasi antar Model (Eloquent).
3.  **Tahap 3: Autentikasi & Routing**
    *   Implementasi sistem Login dan Middleware (Admin, Guru, Ortu).
4.  **Tahap 4: Halaman Publik (Frontend)**
    *   Desain dan integrasi data dinamis (dari database) ke Halaman Home.
5.  **Tahap 5: Fitur Admin**
    *   CRUD Data Guru, Siswa, Ortu, dan Jadwal.
    *   Pengaturan Profil Les & Testimoni.
    *   Fitur Cetak Sertifikat & Cetak Bintang (PDF generation).
6.  **Tahap 6: Fitur Guru**
    *   Modul penetapan Materi & Target.
    *   Formulir pencatatan Kegiatan (Log Book siswa).
7.  **Tahap 7: Fitur Ortu**
    *   Dashboard monitoring progress siswa (Read-only view).
8.  **Tahap 8: Finishing & Testing**
    *   UI/UX Refinement (Tailwind modern & elegant).
    *   Bug fixing dan Uji Coba alur kerja.
