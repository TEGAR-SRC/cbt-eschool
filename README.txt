===========================================
    APLIKASI EduPus - README FILE
===========================================

Panduan Instalasi Aplikasi EduPus
===========================================

Aplikasi ini dibuat menggunakan PHP 8 dan JavaScript.
Ikuti langkah-langkah berikut untuk melakukan instalasi di komputer lokal Anda menggunakan XAMPP.

1. Install XAMPP (PHP 8)
-------------------------
- Unduh XAMPP dari situs resmi:
  https://www.apachefriends.org/index.html
- Pilih versi XAMPP yang mendukung PHP 8.x
- Install XAMPP seperti biasa (gunakan lokasi default: C:\xampp)
- Jalankan XAMPP Control Panel, lalu klik "Start" untuk Apache dan MySQL

2. Salin Folder Aplikasi ke htdocs
----------------------------------
- Ekstrak folder aplikasi EduPus (misalnya: edupus)
- Salin folder tersebut ke direktori:
  C:\xampp\htdocs\edupus
- Hapus File C:\xampp\htdocs\edupus\koneksi\koneksi.php

3. Akses Halaman Instalasi
--------------------------
- Buka browser dan akses:
  http://localhost/edupus/install

4. Isi Form Database
--------------------
- Masukkan informasi database sebagai berikut:
  - Host: localhost
  - Nama Database: edupus.id
  - Username: root
  - Password: (kosongkan jika default)
- Klik tombol "Lanjut" atau "Install"
- Sistem akan otomatis membuat database dan tabel

5. Buat Akun Admin Aplikasi
---------------------------
- Isi form admin dengan:
  - Username: (misalnya administrator)
  - Password: (bebas)
  - Nama Lengkap: (misalnya Administrator)
- Klik tombol "Simpan" atau "Selesai"

6. Instalasi Selesai
--------------------
- Anda akan melihat pesan bahwa instalasi berhasil
- Klik tombol "Login" untuk masuk ke aplikasi
- Login menggunakan akun yang baru dibuat

7. Hapus Folder install (DIREKOMENDASIKAN)
------------------------------------------
- Demi keamanan, hapus folder "install" dari:
  C:\xampp\htdocs\edupus\install

8. Akses Aplikasi
-----------------
- Gunakan browser dan buka:
  http://localhost/edupus

===========================================
Selamat menggunakan Aplikasi EduPus!
===========================================

Copyright © 2025
Dikembangkan oleh EduPus
Semua hak cipta dilindungi undang-undang.
