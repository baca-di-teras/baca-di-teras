# Data Migration Integration

Dokumen ini menjelaskan perubahan migrasi data 4 perpustakaan sekolah ke database utama `bacaditeras`.
Fokus perubahan ini adalah membuat asal data menjadi entity yang jelas, bukan hanya bergantung pada angka offset.

## Tujuan

- Menggabungkan data koleksi dari 4 database sekolah ke satu database SLiMS 9 `bacaditeras`.
- Menjaga struktur SLiMS 9 tanpa mengubah core SLiMS.
- Menghindari tabrakan primary key antar database source.
- Menjadikan lokasi/source perpustakaan sebagai entity yang bisa ditelusuri.
- Menyediakan tabel audit untuk mapping `old_id` dari database source ke `new_id` di target.

## Source Perpustakaan

| Source Code | Nama Perpustakaan | Staging DB | Tipe Source | Offset |
|-------------|-------------------|------------|-------------|--------|
| SBJ | SD Banjarsari | temp_sdbanjarsari | SLiMS | 100000 |
| SD2 | SD Negeri 2 Teras | temp_sdn2teras | INLIS | 200000 |
| S1T | SMP Negeri 1 Teras | temp_smpn1teras | SLiMS | 300000 |
| S2T | SMP Negeri 2 Teras | temp_smpn2teras | INLIS | 400000 |

## Entity Lokasi dan Source

Setiap source perpustakaan direpresentasikan melalui beberapa kolom dan tabel:

| Lokasi Data | Fungsi |
|-------------|--------|
| `bdt_library` | Entity utama profil perpustakaan di layer custom. |
| `bdt_library.slims_location_id` | Kode lokasi yang menghubungkan entity custom ke SLiMS. |
| `mst_location.location_id` | Kode lokasi di tabel master SLiMS. |
| `biblio.source` | Kode asal data bibliografi. |
| `item.location_id` | Lokasi fisik item/eksemplar. |
| `member.member_id` | Prefix source untuk member hasil migrasi, contoh `SBJ-...`. |
| `bdt_migration_map.library_id` | Foreign key ke entity `bdt_library`. |

Dengan struktur ini, data dapat ditelusuri berdasarkan perpustakaan asal, bukan hanya berdasarkan range ID.

## Mapping Table

Script migrasi membuat tabel audit:

```sql
CREATE TABLE bdt_migration_map (
    map_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    library_id INT(11) NOT NULL,
    source_code VARCHAR(10) NOT NULL,
    source_database VARCHAR(64) NOT NULL,
    source_type VARCHAR(20) NOT NULL,
    source_table VARCHAR(64) NOT NULL,
    old_id VARCHAR(100) NOT NULL,
    target_table VARCHAR(64) NOT NULL,
    new_id VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (map_id)
);
```

Kolom penting:

- `library_id`: entity perpustakaan asal di `bdt_library`.
- `source_code`: kode source seperti `SBJ`, `SD2`, `S1T`, `S2T`.
- `source_database`: nama database staging.
- `source_table`: tabel atau field source asal.
- `old_id`: ID lama dari database source.
- `target_table`: tabel target di `bacaditeras`.
- `new_id`: ID baru setelah migrasi.

Contoh trace topic:

```sql
SELECT m.*, l.name
FROM bdt_migration_map m
JOIN bdt_library l ON l.library_id = m.library_id
WHERE m.target_table = 'mst_topic'
  AND m.new_id = '101166';
```

Hasilnya menunjukkan bahwa `topic_id = 101166` berasal dari:

```text
source_code: SBJ
source_database: temp_sdbanjarsari
source_table: mst_topic
old_id: 1166
library: SD Banjarsari
```

## Kenapa Offset Masih Dipakai

`source/location identifier` menjelaskan asal data, tetapi primary key SLiMS tetap harus unik dalam satu database.
Karena itu migrasi masih memakai offset untuk tabel yang primary key-nya numerik.

Contoh:

```text
temp_sdbanjarsari.mst_topic.topic_id = 1166
offset SBJ = 100000
target bacaditeras.mst_topic.topic_id = 101166
```

Offset bukan satu-satunya sumber kebenaran. Tabel `bdt_migration_map` menjadi sumber audit untuk mengetahui mapping sebenarnya.

## Validasi Block Offset

Script menggunakan block size `100000`.
Jika ID source melewati batas block, migrasi akan dihentikan agar tidak terjadi overlap diam-diam.

Contoh risiko:

```text
SBJ offset = 100000
old_id = 100001
new_id = 200001
```

`200001` masuk ke block `SD2`, sehingga ini tidak boleh dibiarkan.
Script sekarang melakukan preflight validation sebelum proses clear/import.

## Alur Migrasi

Script utama:

```text
custom/scripts/migrate_data.php
```

Alur eksekusi:

1. Memastikan semua staging database tersedia.
2. Membuat/memperbarui reference data seperti `mst_location` dan status item.
3. Membuat tabel `bdt_migration_map` jika belum ada.
4. Melakukan validasi range ID source agar tidak melewati block offset.
5. Menghapus data migrasi sebelumnya.
6. Menyinkronkan entity perpustakaan di `bdt_library`.
7. Mengambil `library_id` untuk setiap source.
8. Memigrasikan data SLiMS source.
9. Memigrasikan data INLIS source.
10. Menyinkronkan total koleksi dan anggota di `bdt_library`.
11. Mencetak ringkasan verifikasi.

## Mapping Data SLiMS

Untuk source bertipe SLiMS:

| Source Table | Target Table | Catatan |
|--------------|--------------|---------|
| `biblio` | `biblio` | `biblio_id` diberi offset, `source` diisi kode perpustakaan. |
| `item` | `item` | `item_id` diberi offset, `location_id` diisi kode perpustakaan. |
| `mst_author` | `mst_author` | ID diberi offset, author yang sama dapat reuse ID existing. |
| `mst_publisher` | `mst_publisher` | ID diberi offset, publisher yang sama dapat reuse ID existing. |
| `mst_topic` | `mst_topic` | ID diberi offset, topic yang sama dapat reuse ID existing. |
| `biblio_author` | `biblio_author` | Relasi memakai ID hasil mapping. |
| `biblio_topic` | `biblio_topic` | Relasi memakai ID hasil mapping. |
| `member` | `member` | `member_id` diberi prefix source. |
| `loan` | `loan` | Loan dimigrasi jika item dan member source dapat dipetakan. |

## Mapping Data INLIS

Untuk source bertipe INLIS:

| Source Table/Field | Target Table | Catatan |
|--------------------|--------------|---------|
| `catalogs` | `biblio` | `ID` diberi offset menjadi `biblio_id`. |
| `collections` | `item` | `ID` diberi offset menjadi `item_id`. |
| `catalogs.Author` | `mst_author` | Dibuat sebagai generated author mulai dari `800000`. |
| `catalogs.Publisher` | `mst_publisher` | Dibuat sebagai generated publisher mulai dari `850000`. |
| `catalogs.Subject` | `mst_topic` | Dibuat sebagai generated topic mulai dari `900000`. |
| `members` | `member` | `member_id` diberi prefix source. |
| `collectionloanitems` | `loan` | Dimigrasi jika data member dapat dipetakan. |

## Cara Menjalankan

Pastikan MySQL/MariaDB XAMPP aktif dan staging database sudah ada.

Untuk macOS XAMPP:

```bash
/Applications/XAMPP/xamppfiles/bin/php custom/scripts/migrate_data.php
```

Output sukses terakhir:

```text
Migrasi selesai.

Verifikasi target
- total biblio migrasi: 5248
- total item migrasi: 20091
- total member migrasi: 1579
- total loan migrasi: 6523
- SBJ SD Banjarsari: 1870 judul, 9163 item, 13724 map, status aktif
- SD2 SD Negeri 2 Teras: 225 judul, 554 item, 1287 map, status aktif
- S1T SMP Negeri 1 Teras: 2978 judul, 8359 item, 21202 map, status aktif
- S2T SMP Negeri 2 Teras: 115 judul, 2015 item, 3042 map, status aktif
```

## Query Verifikasi

Hitung mapping per source:

```sql
SELECT source_code, source_database, source_type, COUNT(*) AS map_rows
FROM bdt_migration_map
GROUP BY source_code, source_database, source_type
ORDER BY source_code;
```

Cek jumlah target dan mapping:

```sql
SELECT 'biblio_target' AS metric, COUNT(*) AS total
FROM biblio
WHERE biblio_id >= 100000
UNION ALL
SELECT 'biblio_map', COUNT(*)
FROM bdt_migration_map
WHERE target_table = 'biblio'
UNION ALL
SELECT 'item_target', COUNT(*)
FROM item
WHERE item_id >= 100000
UNION ALL
SELECT 'item_map', COUNT(*)
FROM bdt_migration_map
WHERE target_table = 'item';
```

Cek total koleksi per entity perpustakaan:

```sql
SELECT
    l.library_id,
    l.slims_location_id,
    l.name,
    l.total_koleksi,
    COUNT(DISTINCT i.biblio_id) AS actual_titles,
    COUNT(i.item_id) AS actual_items
FROM bdt_library l
LEFT JOIN item i
    ON i.location_id = CONVERT(l.slims_location_id USING utf8) COLLATE utf8_unicode_ci
WHERE l.slims_location_id IN ('SBJ', 'SD2', 'S1T', 'S2T')
GROUP BY l.library_id, l.slims_location_id, l.name, l.total_koleksi
ORDER BY l.slims_location_id;
```

Cek orphan item:

```sql
SELECT i.item_id, i.biblio_id, i.item_code, i.location_id
FROM item i
LEFT JOIN biblio b ON b.biblio_id = i.biblio_id
WHERE i.location_id IN ('SBJ', 'SD2', 'S1T', 'S2T')
  AND b.biblio_id IS NULL;
```

Saat verifikasi terakhir terdapat satu item orphan dari source:

```text
item_id: 301640
location_id: S1T
source_database: temp_smpn1teras
source_table: item
old_id: 1640
```

Item ini tidak muncul di katalog karena tidak memiliki pasangan `biblio`.

## UI Alignment

Halaman katalog dan detail perpustakaan membaca data dari database hasil migrasi:

| UI | Data Source |
|----|-------------|
| `/katalog` | `biblio`, `item`, `bdt_library`, `mst_topic`, `mst_publisher` |
| `/katalog?location=SBJ` | Filter dari `item.location_id` |
| `/perpustakaan/{slug}` | `bdt_library.slug` dan `bdt_library.slims_location_id` |
| Filter kategori | `mst_topic` yang benar-benar terhubung ke `biblio_topic` dan item aktif |
| Status stok | Agregasi dari `item` dan `loan` |

Dengan ini, UI tidak bergantung pada urutan ID.
UI menggunakan nama topic, source/location, dan relasi database.

## Catatan Maintenance

- Jangan import SQL source langsung ke `bacaditeras`.
- Import SQL source ke staging database terlebih dahulu.
- Jalankan script migrasi dari `custom/scripts`.
- Jangan mengubah SLiMS core untuk kebutuhan migrasi.
- Jika jumlah source bertambah, tambahkan entry baru di `$sources` dan gunakan block offset baru yang tidak bentrok.
- Jika ingin menghilangkan limit block offset sepenuhnya, migrasi harus diubah ke strategi `AUTO_INCREMENT + bdt_migration_map`.
