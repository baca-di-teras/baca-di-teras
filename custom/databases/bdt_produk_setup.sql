-- Database Setup untuk Modul Produk (Galeri Produk)

CREATE TABLE IF NOT EXISTS `bdt_produk` (
  `produk_id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `group_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `image_1` VARCHAR(255) NOT NULL,
  `image_2` VARCHAR(255) DEFAULT NULL,
  `image_3` VARCHAR(255) DEFAULT NULL,
  `is_visible` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menambahkan Data Contoh
INSERT INTO `bdt_produk` (`title`, `group_name`, `description`, `image_1`, `image_2`, `image_3`, `is_visible`) VALUES
('Tas Pustaka', 'KELOMPOK 1', 'Tas kanvas organik yang dirancang khusus untuk membawa buku dan perangkat digital Anda. Tahan lama, ramah lingkungan, dan merepresentasikan semangat belajar di mana saja.', 'custom/assets/images/produk_dummy_1.jpg', 'custom/assets/images/produk_dummy_2.jpg', 'custom/assets/images/produk_dummy_3.jpg', 1),
('Kaos Baca', 'KELOMPOK 2', 'Kaos katun premium yang nyaman digunakan saat santai membaca di teras. Desain minimalis yang merayakan kecintaan pada literasi.', 'custom/assets/images/produk_dummy_4.jpg', 'custom/assets/images/produk_dummy_5.jpg', 'custom/assets/images/produk_dummy_6.jpg', 1),
('Tumblr Teras', 'KELOMPOK 3', 'Temani waktu baca Anda dengan minuman favorit yang tetap terjaga suhunya. Botol minum ramah lingkungan untuk gaya hidup berkelanjutan.', 'custom/assets/images/produk_dummy_7.jpg', 'custom/assets/images/produk_dummy_8.jpg', NULL, 1);
