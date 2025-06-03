# 📘 Aplikasi CBT eSchool - README

## Panduan Instalasi Aplikasi CBT eSchool

Aplikasi CBT eSchool adalah sistem Computer-Based Test berbasis **PHP 8** dan **JavaScript**. Panduan berikut membantu Anda melakukan instalasi di komputer lokal menggunakan **XAMPP**.

---

### 1. Install XAMPP (PHP 8)
- Unduh XAMPP dari situs resmi: [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)
- Pilih versi XAMPP yang mendukung **PHP 8.x**
- Install seperti biasa (lokasi default: `C:\xampp`)
- Jalankan **XAMPP Control Panel** dan klik **Start** pada **Apache** dan **MySQL**

---

### 2. Salin Folder Aplikasi ke `htdocs`
- Ekstrak folder aplikasi (contoh: `cbt-eschool`)
- Pindahkan folder ke:  
  `C:\xampp\htdocs\cbt-eschool`
- Hapus `File C:\xampp\htdocs\cbt-eschool\koneksi\koneksi.php`

---

### 3. Akses Halaman Instalasi
- Buka browser dan akses:
`http://localhost/cbt-eschool/install`


---

### 4. Isi Form Database
- Gunakan data berikut:
- **Host**: `localhost`
- **Nama Database**: `cbt_eschool`
- **Username**: `root`
- **Password**: *(kosongkan jika default)*
- Klik **Lanjut / Install**
- Sistem akan otomatis membuat database dan tabel

---

### 5. Buat Akun Admin Aplikasi
- Masukkan informasi:
- **Username**: `administrator` *(atau sesuai keinginan)*
- **Password**: bebas
- **Nama Lengkap**: `Administrator`
- Klik **Simpan / Selesai**

---

### 6. Instalasi Selesai
- Akan muncul pesan bahwa instalasi berhasil
- Klik **Login**
- Login menggunakan akun admin yang telah dibuat

---

### 7. Hapus Folder `install` (Direkomendasikan)
- Demi keamanan, hapus folder:  
`C:\xampp\htdocs\cbt-eschool\install`

---

### 8. Akses Aplikasi
- Gunakan browser dan buka:
`http://localhost/cbt-eschool`


---
### 9. Pastikan Extensi GD aktif
# 📷 Cara Mengaktifkan Ekstensi GD di XAMPP (Windows)

Ekstensi **GD** pada PHP digunakan untuk manipulasi gambar, seperti membuat CAPTCHA, thumbnail, watermark, dan lainnya. Jika Anda menggunakan XAMPP dan mendapatkan error seperti:

> "The image cannot be displayed because it contains errors"

...kemungkinan besar ekstensi GD belum diaktifkan.

---

## 🔧 Langkah-langkah Mengaktifkan GD Extension

### 1. Buka File `php.ini`

- Masuk ke folder:xampp\php\php.ini

- Gunakan editor teks seperti Notepad, Notepad++, VS Code, dll.

---

### 2. Cari dan Edit Baris GD Extension

- Cari baris:
```ini
;extension=gd

    Hapus tanda titik koma ; di depannya agar menjadi:

    extension=gd

Tanda ; menandakan baris tersebut dikomentari. Menghapusnya akan mengaktifkan ekstensi.
3. Simpan Perubahan dan Tutup File
4. Restart Apache di XAMPP

    Buka XAMPP Control Panel

    Klik Stop pada Apache

    Lalu klik Start lagi
    
---
# Screenshots Aplikasi CBT eSchool

## Dashboard Admin  
![Dashboard Admin](https://i.imgur.com/u0IX0Zi.png)

## Manajemen Soal  
![Manajemen Soal](https://i.imgur.com/ybX16a0.png)

## Print Soal  
![Print Soal](https://i.imgur.com/pxwfIUf.png)

## Dashboard Siswa  
![Dashboard Siswa](https://i.imgur.com/6tCz8aI.png)

## Mini Games  
![Mini Games](https://i.imgur.com/PjE6HHo.png)

## ChatBox  
![ChatBox](https://i.imgur.com/Vxb4Sor.png)

## Tampilan Ujian 1  
![Tampilan Ujian 1](https://i.imgur.com/toxWv1S.png)

## Tampilan Ujian 2  
![Tampilan Ujian 2](https://i.imgur.com/0eI7HNK.png)

## ✅ Selamat Menggunakan Aplikasi CBT eSchool!

---

© 2025 — Dikembangkan oleh **Gludug Codelite**  
Semua hak cipta dilindungi undang-undang.