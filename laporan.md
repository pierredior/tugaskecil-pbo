# **LAPORAN PRAKTIKUM**
### Mata Kuliah: Pemrograman Web
#### Topik: Aplikasi Inventory Management "MerchShipe" dengan Metode Terstruktur

---
### **Identitas Praktikan**
- **Nama** :
- **NIM** :
- **Kelas** :
- **Tanggal Praktikum** :
- **Waktu Praktikum** :
---
## **1. Pendahuluan**

### **1.1 Latar Belakang**
Dalam perkembangan teknologi informasi, sistem manajemen inventaris menjadi kebutuhan dasar dalam aplikasi web. Sistem login berbasis peran (role-based access control) dan fitur CRUD (Create, Read, Update, Delete) merupakan komponen inti dari hampir semua sistem informasi, terutama di lingkungan bisnis seperti manajemen barang, toko, supplier, dan kategori produk.

Aplikasi ini dibangun menggunakan **PHP struktural (prosedural)** tanpa framework, dengan tujuan memahami secara mendalam mekanisme dasar pengelolaan sesi, autentikasi, koneksi database, dan keamanan aplikasi web. Pendekatan ini sangat relevan bagi mahasiswa yang ingin memahami fondasi pemrograman web sebelum beralih ke framework modern seperti Laravel.

### **1.2 Tujuan**
- Membangun sistem login dengan autentikasi berbasis peran (`admin`, `user`).
- Mengimplementasikan fitur CRUD untuk manajemen data barang.
- Mengimplementasikan fitur CRUD untuk manajemen data kategori
- Mengimplementasikan fitur CRUD untuk manajemen data supplier
- Mengimplementasikan fitur CRUD untuk manajemen data toko
- Menggunakan PDO dan konstanta PHP untuk keamanan konfigurasi database.
- Menerapkan prinsip keamanan: password hashing, CSRF protection, dan SQL injection prevention.
- Membangun arsitektur proyek yang terstruktur dan modular tanpa menggunakan OOP atau framework.

### **1.3 Ruang Lingkup**
Aplikasi ini mencakup:
- Sistem login dan registrasi pengguna.
- Role-based dashboard: admin dan user
- Manajemen barang, kategori, supplier, dan toko (CRUD)
- Proteksi konfigurasi database menggunakan file PHP.
- Penggunaan Tailwind CSS dengan DaisyUI untuk tampilan responsif.
- Penanganan error dan redirect yang aman tanpa loop.

---

## **2. Landasan Teori**

### **2.1 PHP Struktural (Prosedural)**
PHP struktural adalah paradigma pemrograman dalam PHP di mana program dibangun berdasarkan fungsi-fungsi atau prosedur tanpa menggunakan konsep OOP (Object-Oriented Programming). Dalam pendekatan ini, kode program disusun secara linear dan fokus pada alur eksekusi prosedural. Fungsi-fungsi digunakan untuk memecah tugas-tugas kompleks menjadi bagian-bagian yang lebih kecil dan mudah dikelola. Pendekatan ini sangat bermanfaat untuk memahami dasar-dasar pemrograman web sebelum mempelajari framework yang lebih kompleks karena kita dapat melihat secara langsung bagaimana setiap komponen bekerja dan berinteraksi satu sama lain.

### **2.2 PDO dan Prepared Statement**
PDO (PHP Data Objects) adalah ekstensi PHP yang menyediakan antarmuka untuk berinteraksi dengan berbagai database termasuk MySQL. Prepared Statement adalah metode yang digunakan untuk mengeksekusi query SQL secara aman dengan memisahkan struktur query dari data yang dimasukkan. Dengan menggunakan prepared statement, kita dapat mencegah SQL injection karena parameter diproses secara terpisah dari query utama. Dalam aplikasi ini, prepared statement digunakan secara konsisten untuk semua operasi CRUD untuk memastikan keamanan dari serangan injeksi SQL.

### **2.3 Password Hashing dengan `password_hash()`**
Fungsi `password_hash()` adalah fungsi bawaan PHP yang digunakan untuk mengenkripsi password pengguna secara aman menggunakan algoritma hashing yang kuat seperti bcrypt. Password yang disimpan dalam database tidak dalam bentuk plaintext melainkan dalam bentuk hash yang tidak dapat dikembalikan ke bentuk aslinya. Ini adalah praktik keamanan yang penting untuk melindungi informasi sensitif pengguna. Fungsi `password_verify()` digunakan untuk memverifikasi apakah password yang dimasukkan oleh pengguna cocok dengan hash yang tersimpan di database.

### **2.4 Session dan Cookie**
Session adalah mekanisme server-side untuk menyimpan informasi sementara tentang pengguna yang sedang login ke dalam suatu aplikasi web. Data session disimpan di server dan diidentifikasi melalui ID session yang biasanya disimpan dalam cookie di sisi client. Session digunakan untuk menjaga status login pengguna selama sesi berlangsung dan untuk menyimpan informasi seperti peran (role) pengguna. Cookie adalah data kecil yang disimpan di browser pengguna dan dikirim kembali ke server dengan setiap permintaan, sering digunakan untuk menyimpan ID session atau preferensi pengguna.

### **2.5 CSRF Protection**
CSRF (Cross-Site Request Forgery) adalah serangan keamanan yang memanipulasi pengguna terotentikasi untuk melakukan tindakan yang tidak diinginkan. CSRF protection dilakukan dengan cara menghasilkan token unik untuk setiap sesi atau formulir dan memastikan bahwa semua request POST dilengkapi dengan token yang valid. Dalam aplikasi ini, token CSRF dihasilkan dan disimpan dalam session serta divalidasi saat menerima data dari formulir untuk mencegah serangan CSRF.

### **2.6 Konfigurasi Database dan .htaccess**
Konfigurasi database disimpan dalam file PHP menggunakan konstanta untuk menyimpan kredensial seperti host, nama database, username, dan password. File .htaccess adalah konfigurasi Apache yang memungkinkan kontrol terhadap perilaku server web, termasuk pembatasan akses file, pengalihan URL, dan pengamanan direktori. Kombinasi ini memberikan lapisan keamanan tambahan pada aplikasi dengan menyembunyikan detail sensitif konfigurasi database.

### **2.7 Tailwind CSS dengan DaisyUI**
Tailwind CSS adalah framework CSS utility-first yang menyediakan kelas-kelas kecil untuk membangun antarmuka secara langsung di HTML. DaisyUI adalah plugin Tailwind yang menyediakan komponen UI siap pakai seperti tombol, kartu, dan formulir. Kombinasi ini memungkinkan pembuatan tampilan web responsif dan modern tanpa harus menulis banyak CSS dari awal.

---

## **3. Metodologi**

### **3.1 Desain Arsitektur Proyek**

```
/project-root
├── .htaccess
├── login.php
├── register.php
├── logout.php
├── index.php
├── admin/
│   └── index.php
├── user/
│   └── index.php
├── barang/
│    ├── index.php
│    ├── add.php
│    ├── edit.php
│    └── delete.php
├── kategori/
│    ├── index.php
│    ├── add.php
│    ├── edit.php
│    └── delete.php
├── supplier/
│    ├── index.php
│    ├── add.php
│    ├── edit.php
│    └── delete.php
├── toko/
│    ├── index.php
│    ├── add.php
│    ├── edit.php
│    └── delete.php
├── views/
│    ├── header.php
│    ├── footer.php
│    ├── sidebar.php
│    └── topnav.php
├── config/
│   └── database.php
└── assets/
    ├── css/
    ├── images/
    └── js/
```

### **3.2 Alur Sistem**

1. **Pengguna mengakses `/login.php`**
2. Jika sudah login → redirect ke dashboard sesuai role.
3. Jika belum login → isi form username/email dan password.
4. Setelah login → session di-set, role dicek → redirect ke `/admin/` atau `/user/`
5. Akses ke halaman CRUD hanya diizinkan untuk pengguna yang login.
6. Admin memiliki akses penuh ke semua fitur.
7. User memiliki akses terbatas tergantung konfigurasi.
8. CRUD barang/kategori/supplier/toko dilakukan melalui form yang dilindungi CSRF token.
9. Logout → sesi dihapuskan → redirect ke login.

### **3.3 Teknologi yang Digunakan**

| Komponen    | Teknologi                                                        |
| ----------- | ---------------------------------------------------------------- |
| Backend     | PHP 8.1+ (struktural)                                            |
| Database    | MySQL 8.0                                                        |
| Frontend    | HTML5, CSS3, Tailwind CSS, DaisyUI                               |
| Koneksi DB  | PDO dengan Prepared Statement                                    |
| Keamanan    | `password_hash()`, CSRF token, `.htaccess`, `htmlspecialchars()` |
| Konfigurasi | PHP Constants                                                    |
| Editor      | Visual Studio Code                                               |

### **3.4 Database Schema**

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE barang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_barang VARCHAR(255) NOT NULL,
  harga DECIMAL(10,2) NOT NULL,
  stok INT NOT NULL DEFAULT 0,
  id_kategori INT NOT NULL,
  id_supplier INT NOT NULL,
  id_toko INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_kategori) REFERENCES kategori(id),
  FOREIGN KEY (id_supplier) REFERENCES supplier(id),
  FOREIGN KEY (id_toko) REFERENCES toko(id)
);

CREATE TABLE kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL,
  deskripsi TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE supplier (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_supplier VARCHAR(100) NOT NULL,
  alamat VARCHAR(255),
  telepon VARCHAR(20),
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE toko (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_toko VARCHAR(100) NOT NULL,
  alamat VARCHAR(255),
  telepon VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  action VARCHAR(50) NOT NULL,
  description TEXT,
  ip_address VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## **4. Hasil dan Pembahasan**

### **4.1 Hasil Implementasi**

#### **4.1.1 Halaman Login & Registrasi**
- Tampilan menggunakan Tailwind CSS dengan DaisyUI components, responsif, dan intuitif.
- Password di-hash sebelum disimpan ke database.
- Validasi input dan pesan error tampil secara dinamis.

#### **4.1.2 Dashboard Berbasis Role**
- Setiap role memiliki dashboard sendiri (`admin/`, `user/`).
- Admin memiliki akses penuh ke manajemen barang, kategori, supplier, dan toko.
- User memiliki akses terbatas (dapat diperluas sesuai kebutuhan).

#### **4.1.3 Fitur CRUD**
| Fitur      | Fungsi                                                 |
| ---------- | ------------------------------------------------------ |
| **Index**  | Menampilkan daftar data dengan pagination (sederhana). |
| **Add**    | Form untuk menambah data baru dengan validasi wajib.   |
| **Edit**   | Form untuk memperbarui data.                           |
| **Delete** | Hapus data dengan konfirmasi.                          |

#### **4.1.4 Keamanan**
- Konfigurasi database terlindungi melalui file PHP terpisah.
- Semua input di-sanitasi dengan `htmlspecialchars()` dan filter input.
- CSRF token di-generate per sesi dan diverifikasi di setiap form POST.
- Password di-hash dengan bcrypt → tidak ada penyimpanan password plaintext.

#### **4.1.5 Antarmuka**
- Menggunakan Tailwind CSS dengan DaisyUI untuk tampilan modern.
- Sidebar dan topnav konsisten di semua halaman.

### **4.2 Pembahasan**

- **Keberhasilan**:
Aplikasi berhasil menerapkan sistem login berbasis peran dengan 2 jenis role (admin dan user) sesuai dengan spesifikasi. Fungsi CRUD berjalan dengan baik untuk empat entitas data (barang, kategori, supplier, dan toko) dengan pengamanan CSRF token dan validasi input yang ketat. Akses berdasarkan role juga diterapkan dengan benar sehingga pengguna hanya bisa mengakses fitur yang sesuai dengan perannya. Sistem logging aktivitas pengguna juga diterapkan untuk keperluan audit.

- **Tantangan**:
Beberapa tantangan yang dihadapi antara lain: pengelolaan session dan role secara manual tanpa framework, pencegahan SQL injection melalui prepared statement secara konsisten di semua fungsi, dan implementasi CSRF protection yang membutuhkan pembuatan token unik untuk setiap sesi. Selain itu, menghubungkan relasi antar tabel untuk menampilkan informasi terkait juga membutuhkan query yang kompleks.

- **Solusi yang Diterapkan**:
Kami menerapkan pendekatan modular dengan memisahkan fungsi autentikasi dan database ke file `config/database.php`. Penggunaan prepared statement secara konsisten di semua operasi database mencegah SQL injection. CSRF token dihasilkan dan divalidasi di setiap formulir POST. Sistem logging aktivitas disimpan dalam tabel terpisah untuk keperluan audit dan monitoring.

- **Perbandingan dengan Framework**:
Dengan pendekatan struktural yang kami gunakan, konfigurasi dan alur aplikasi menjadi lebih jelas dan transparan dibandingkan menggunakan framework seperti Laravel atau CodeIgniter. Namun, kode yang dihasilkan lebih verbose dan membutuhkan lebih banyak baris untuk implementasi fungsi yang sama. Framework menyediakan built-in protection untuk CSRF, otentikasi, dan validasi, sedangkan dalam pendekatan struktural ini semua harus diimplementasikan secara manual.

---

## **5. Kesimpulan**

1. Aplikasi sistem inventory "MerchShipe" berhasil dibangun menggunakan **PHP struktural** tanpa framework.
2. Keamanan aplikasi terjamin melalui **hashing password**, **CSRF token**, **validasi role**, dan **prepared statements**.
3. Arsitektur proyek yang terstruktur memudahkan pengembangan, pemeliharaan, dan pembelajaran.
4. Pendekatan ini sangat efektif untuk **pembelajaran dasar pemrograman web**, terutama bagi mahasiswa yang perlu memahami fondasi aplikasi web sebelum beralih ke teknik yang lebih modern (framework PHP)

---

## **6. Lampiran**

### **Lampiran A: Screenshoot Aplikasi**
*(Sisipkan 4–6 screenshot berikut:)*
1. Halaman Login
2. Halaman Register
3. Dashboard Admin
4. Dashboard User
5. Halaman Tampil Barang
6. Halaman Tambah Barang
7. Halaman Edit Barang
8. Halaman Tampil Kategori
9. Halaman Tambah Kategori
10. Halaman Edit Kategori
11. Halaman Tampil Supplier
12. Halaman Tambah Supplier
13. Halaman Edit Supplier
14. Halaman Tampil Toko
15. Halaman Tambah Toko
16. Halaman Edit Toko

### **Lampiran B: Kode Sumber**
- File `config/database.php`
- File `login.php`
- File `register.php`
- File `barang/index.php`
- File `kategori/index.php`
- File `.htaccess`

### **Lampiran C: Hasil Testing**
| Uji Coba                            | Hasil                                      |
| ----------------------------------- | ------------------------------------------ |
| Login sebagai admin                 | ✅ Berhasil → redirect ke /admin            |
| Login sebagai user                  | ✅ Berhasil → redirect ke /user             |
| Registrasi akun baru               | ✅ Berhasil - akun dibuat dengan role 'user' |
| Akses CRUD barang sebagai admin     | ✅ Berhasil - akses penuh                   |
| Akses CRUD barang sebagai user      | ✅ Berhasil - akses tergantung konfigurasi  |
| Input SQL injection di login        | ✅ Tidak berhasil → di-sanitasi             |
| Akses langsung ke /barang/add.php   | ✅ Redirect ke login jika belum login       |
| Validasi input kosong               | ✅ Berhasil - pesan error ditampilkan       |

---

## **7. Daftar Pustaka**

1. PHP.net. (2023). *PHP: PDO - Manual*. Diakses dari https://www.php.net/manual/en/book.pdo.php
2. Tailwind Labs. (2023). *Tailwind CSS Documentation*. Diakses dari https://tailwindcss.com/docs
3. Sinau, P. (2023). *DaisyUI - Component Library*. Diakses dari https://daisyui.com/
4. Weber, S. (2022). *Web Security: Cross-Site Request Forgery Protection*. InformIT.
5. Beaulieu, A. (2021). *Learning SQL: Generate, Manipulate, and Retrieve Data*. O'Reilly Media.

---

**Praktikan,**
[Nama Mahasiswa]
NIM : [nim]

**Mengetahui,**
Dosen Pengampu,


Freddy Wicaksono, S.Kom,  M.Kom

Asisten Praktikum ,


Nurjati, S.T