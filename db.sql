-- LANGKAH 1: Buat database
-- Ganti `nama_database_anda` dengan nama database yang Anda inginkan
CREATE DATABASE `nama_database_anda`;

-- Gunakan database yang baru dibuat
USE `nama_database_anda`;


-- LANGKAH 2: Buat tabel `users`
-- Tabel ini akan menyimpan data pengguna dan kredensial login
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- LANGKAH 3: Buat tabel `products`
-- Tabel ini akan menyimpan data produk
CREATE TABLE `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `imageUrl` VARCHAR(255),
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);