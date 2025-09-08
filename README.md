
# Full-Stack CRUD E-commerce API

Aplikasi web full-stack modern yang mengelola operasi **CRUD** (Create, Read, Update, Delete) untuk produk. Proyek ini dibuat untuk mendemonstrasikan kemampuan pengembangan **front-end**, **back-end**, dan **otentikasi**.

---

## 📚 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Tumpukan Teknologi](#-tumpukan-teknologi)
3. [Struktur Proyek](#-struktur-proyek)
4. [Persyaratan Sistem](#-persyaratan-sistem)
5. [Instalasi & Menjalankan Proyek](#-instalasi--menjalankan-proyek)
6. [Panduan API](#-panduan-api)
7. [Penulis](#-penulis)

---

## 🚀 Fitur Utama
- **Sistem Otentikasi**  
  Pengguna dapat mendaftar dan masuk menggunakan **JWT** untuk akses ke rute yang dilindungi.
- **CRUD Produk**  
  Antarmuka lengkap untuk membuat, melihat, memperbarui, dan menghapus produk.
- **Unggah Gambar**  
  Gambar produk diunggah ke server dan ditampilkan di front-end.
- **Pencarian & Validasi**  
  Pencarian real-time dan validasi data di client & server.
- **Desain Modern**  
  UI responsif & tema gelap menggunakan **Tailwind CSS**.

---

## 🛠 Tumpukan Teknologi

### Backend (Layanan 1: `nestjs-server/`)
- **Kerangka Kerja:** NestJS (TypeScript)
- **ORM:** Prisma  
- **Database:** MySQL  
- **Otentikasi:** JWT (@nestjs/jwt, @nestjs/passport)  
- **Unggah File:** Multer  

### Backend (Layanan 2: `php-server/`)
- **Bahasa:** PHP Native (OOP)  
- **Database:** MySQL (PDO)  
- **Otentikasi:** JWT (firebase/php-jwt)  
- **Manajemen Paket:** Composer  

### Frontend (`reactjs-client/`)
- **Kerangka Kerja:** React (Vite)  
- **State Management:** React Context API  
- **Styling:** Tailwind CSS  
- **Perutean:** react-router-dom  
- **Komunikasi API:** Axios  

---

## 📂 Struktur Proyek

```bash
/fullstack-project/
├── reactjs-client/             # Proyek React (Front-End)
├── nestjs-server/       # Layanan NestJS (Back-End)
├── php-server/          # Layanan PHP Native (Back-End)
├── .gitignore            # Aturan Git untuk semua proyek
├── .env.example          # Template variabel lingkungan
└── README.md             # Dokumentasi proyek
```

---

## 🖥 Persyaratan Sistem
- Node.js: v16 atau lebih baru
- PHP: v8.0 atau lebih baru
- MySQL: v5.7 atau lebih baru
- Composer
- Git

---

## ⚡ Instalasi & Menjalankan Proyek

### 1️⃣ Siapkan Basis Data
Buat database & tabel:
```sql
CREATE DATABASE `nama_database_anda`;
USE `nama_database_anda`;

CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `imageUrl` VARCHAR(255),
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

Salin file `.env.example` ke `.env` di setiap folder backend dan isi kredensial database.

---

### 2️⃣ Menjalankan Backend NestJS
```bash
cd nestjs-server/
npm install
npx prisma migrate dev --name init_db
npm run start:dev
```

---

### 3️⃣ Menjalankan Backend PHP Native
```bash
cd php-server/
composer install
php -S localhost:8000 -t public
```

---

### 4️⃣ Menjalankan Frontend React
```bash
cd react-client/
npm install
npm run dev
```

---

## 📡 Panduan API

| Endpoint                 | Metode   | Deskripsi                        | Otentikasi |
|---------------------------|----------|----------------------------------|------------|
| `/auth/register`          | POST     | Mendaftarkan pengguna baru        | Tidak      |
| `/auth/login`             | POST     | Login & mendapatkan token JWT     | Tidak      |
| `/products`               | GET      | Mengambil semua produk            | Ya         |
| `/products`               | POST     | Membuat produk baru               | Ya         |
| `/products/{id}`          | GET      | Mengambil produk berdasarkan ID   | Ya         |
| `/products/{id}`          | PATCH    | Memperbarui produk berdasarkan ID | Ya         |
| `/products/{id}`          | DELETE   | Menghapus produk berdasarkan ID   | Ya         |
| `/products/upload-image`  | POST     | Mengunggah gambar produk          | Ya         |

---

### Demo Proyek

Tonton video demo singkat dari proyek ini.

[Tonton Video Demo](https://drive.google.com/file/d/1pgu3y9rlldppzq7mbF_bW2DYQuiOda85/view?usp=sharing)

## ✍️ Penulis
Proyek ini dibuat oleh Mulyadi, S.Kom.  

