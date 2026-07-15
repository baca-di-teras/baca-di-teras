# Run and Migration Guide

Dokumen ini adalah panduan operasional untuk menjalankan project Baca Di Teras dan memigrasikan data 4 perpustakaan sekolah.

Gunakan dokumen ini untuk:

- Menjalankan aplikasi di XAMPP.
- Import database terbaru dari repository.
- Rebuild migrasi dari 4 file SQL source sekolah.
- Verifikasi bahwa data migrasi sudah masuk ke UI.
- Update dump SQL setelah migrasi ulang.

## Prasyarat

- XAMPP aktif.
- Apache aktif.
- MySQL/MariaDB aktif.
- Project berada di folder XAMPP `htdocs` jika memakai URL `http://localhost/baca-di-teras`.
- Branch aktif: `feat-datamigration-zoe`.

Path binary XAMPP di macOS:

```bash
/Applications/XAMPP/xamppfiles/bin/php
/Applications/XAMPP/xamppfiles/bin/mysql
/Applications/XAMPP/xamppfiles/bin/mysqldump
```

Konfigurasi database default project:

```text
DB_HOST: localhost
DB_USER: root
DB_PASS: kosong
DB_NAME: bacaditeras
DB_PORT: 3306
```

Konfigurasi URL default:

```text
BASE_URL: /baca-di-teras
RewriteBase: /baca-di-teras/
```

## 1. Ambil Branch Terbaru

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/baca-di-teras
git fetch origin
git checkout feat-datamigration-zoe
git pull
```

Jika project tidak berada di `htdocs`, pindahkan atau clone ke:

```text
/Applications/XAMPP/xamppfiles/htdocs/baca-di-teras
```

URL yang sesuai dengan konfigurasi saat ini:

```text
http://localhost/baca-di-teras
```

## 2. Jalankan Aplikasi dari Dump SQL Terbaru

Gunakan langkah ini jika hanya ingin menjalankan aplikasi dengan data migrasi yang sudah jadi.

File SQL target terbaru:

```text
custom/databases/bacaditeras.sql
```

Import database:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "DROP DATABASE IF EXISTS bacaditeras; CREATE DATABASE bacaditeras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
/Applications/XAMPP/xamppfiles/bin/mysql -uroot bacaditeras < custom/databases/bacaditeras.sql
```

Buka aplikasi:

```text
http://localhost/baca-di-teras
```

Buka katalog:

```text
http://localhost/baca-di-teras/katalog
```

Buka daftar perpustakaan:

```text
http://localhost/baca-di-teras/perpustakaan
```

Dengan jalur ini, Anda tidak perlu menjalankan `migrate_data.php` lagi karena dump `bacaditeras.sql` sudah berisi:

- Data koleksi migrasi.
- Entity perpustakaan di `bdt_library`.
- Kode lokasi di `mst_location`.
- Tabel audit `bdt_migration_map`.

## 3. Rebuild Migrasi dari 4 SQL Source

Gunakan langkah ini jika ingin menjalankan migrasi ulang dari source SQL sekolah.

Source SQL:

```text
custom/databases/sdbanjarsari.sql
custom/databases/sdn2teras.sql
custom/databases/smpn1teras.sql
custom/databases/smpn2teras.sql
```

Staging database yang harus tersedia:

```text
temp_sdbanjarsari
temp_sdn2teras
temp_smpn1teras
temp_smpn2teras
```

Buat ulang database target dan staging:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "DROP DATABASE IF EXISTS bacaditeras; CREATE DATABASE bacaditeras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "DROP DATABASE IF EXISTS temp_sdbanjarsari; CREATE DATABASE temp_sdbanjarsari CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "DROP DATABASE IF EXISTS temp_sdn2teras; CREATE DATABASE temp_sdn2teras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "DROP DATABASE IF EXISTS temp_smpn1teras; CREATE DATABASE temp_smpn1teras CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "DROP DATABASE IF EXISTS temp_smpn2teras; CREATE DATABASE temp_smpn2teras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Import target database base.

Pada branch ini `bacaditeras.sql` sudah berisi data migrasi terbaru. Ini tetap aman untuk migrasi ulang karena script akan menghapus data migrasi lama sebelum import ulang.

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -uroot bacaditeras < custom/databases/bacaditeras.sql
```

Import staging source:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -uroot temp_sdbanjarsari < custom/databases/sdbanjarsari.sql
/Applications/XAMPP/xamppfiles/bin/mysql -uroot temp_sdn2teras < custom/databases/sdn2teras.sql
/Applications/XAMPP/xamppfiles/bin/mysql -uroot temp_smpn1teras < custom/databases/smpn1teras.sql
/Applications/XAMPP/xamppfiles/bin/mysql -uroot temp_smpn2teras < custom/databases/smpn2teras.sql
```

Jalankan migrasi:

```bash
/Applications/XAMPP/xamppfiles/bin/php custom/scripts/migrate_data.php
```

Output sukses yang diharapkan:

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

## 4. Update Dump SQL Setelah Migrasi Ulang

Jika migrasi ulang sudah benar dan ingin menyimpan hasil database ke repository, dump ulang database target:

```bash
/Applications/XAMPP/xamppfiles/bin/mysqldump \
  -uroot \
  --single-transaction \
  --quick \
  --triggers \
  --default-character-set=utf8mb4 \
  --skip-comments \
  --skip-dump-date \
  bacaditeras > custom/databases/bacaditeras.sql
```

Catatan:

- Jangan pakai `--events` jika XAMPP menolak karena event scheduler disabled.
- Jangan pakai `--routines` jika muncul error `mysql.proc is wrong`.
- Project saat ini tidak bergantung pada stored routine.

Rapikan newline akhir file jika `git diff --check` memberi warning:

```bash
perl -0pi -e 's/\n+\z/\n/' custom/databases/bacaditeras.sql
```

Commit dan push:

```bash
git add custom/databases/bacaditeras.sql
git commit -m "data: update bacaditeras migration dump"
git push
```

## 5. Verifikasi Database

Cek jumlah data migrasi:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "
SELECT COUNT(*) AS biblio_migrasi FROM bacaditeras.biblio WHERE biblio_id >= 100000;
SELECT COUNT(*) AS item_migrasi FROM bacaditeras.item WHERE item_id >= 100000;
SELECT COUNT(*) AS mapping_rows FROM bacaditeras.bdt_migration_map;
"
```

Hasil terakhir yang diharapkan:

```text
biblio_migrasi: 5248
item_migrasi: 20091
mapping_rows: 39255
```

Cek total koleksi per perpustakaan:

```sql
SELECT
    l.slims_location_id,
    l.name,
    l.total_koleksi,
    COUNT(DISTINCT i.biblio_id) AS actual_titles,
    COUNT(i.item_id) AS actual_items
FROM bacaditeras.bdt_library l
LEFT JOIN bacaditeras.item i
    ON i.location_id = CONVERT(l.slims_location_id USING utf8) COLLATE utf8_unicode_ci
WHERE l.slims_location_id IN ('SBJ', 'SD2', 'S1T', 'S2T')
GROUP BY l.slims_location_id, l.name, l.total_koleksi
ORDER BY l.slims_location_id;
```

Expected:

```text
SBJ: 1870 judul, 9163 item
SD2: 225 judul, 554 item
S1T: 2978 judul, 8359 item
S2T: 115 judul, 2015 item
```

Cek trace data dari source ke target:

```sql
SELECT m.*, l.name
FROM bacaditeras.bdt_migration_map m
JOIN bacaditeras.bdt_library l ON l.library_id = m.library_id
WHERE m.target_table = 'mst_topic'
  AND m.new_id = '101166';
```

Expected:

```text
source_code: SBJ
source_database: temp_sdbanjarsari
source_table: mst_topic
old_id: 1166
new_id: 101166
library: SD Banjarsari
```

## 6. Verifikasi UI

Buka route berikut:

```text
http://localhost/baca-di-teras/
http://localhost/baca-di-teras/katalog
http://localhost/baca-di-teras/katalog?location=SBJ
http://localhost/baca-di-teras/katalog?location=SD2
http://localhost/baca-di-teras/katalog?location=S1T
http://localhost/baca-di-teras/katalog?location=S2T
http://localhost/baca-di-teras/perpustakaan
http://localhost/baca-di-teras/perpustakaan/sd-banjarsari
http://localhost/baca-di-teras/perpustakaan/sd-negeri-2-teras
http://localhost/baca-di-teras/perpustakaan/smp-negeri-1-teras
http://localhost/baca-di-teras/perpustakaan/smp-negeri-2-teras
```

Expected UI:

- `/katalog` menampilkan ribuan koleksi dari database.
- Filter kategori diambil dari `mst_topic` yang terhubung ke `biblio_topic`.
- Filter lokasi memakai `item.location_id`.
- Detail perpustakaan menampilkan buku sesuai lokasi/source.
- Tidak ada `Object not found`, `Warning`, `Fatal error`, atau `SQLSTATE`.

## 7. Smoke Test via CLI

Jika ingin cek route tanpa browser:

```bash
/Applications/XAMPP/xamppfiles/bin/php -r '$_SERVER["REQUEST_URI"]="/katalog"; $_SERVER["REQUEST_METHOD"]="GET"; chdir("/Applications/XAMPP/xamppfiles/htdocs/baca-di-teras"); require "index.php";' >/tmp/bdt_katalog.html
rg -n "Warning|Fatal|Notice|SQLSTATE|Object not found" /tmp/bdt_katalog.html
```

Jika tidak ada output dari `rg`, route render tanpa error PHP yang terlihat.

## 8. Common Problems

### `Object not found` di `http://localhost/baca-di-teras`

Penyebab umum:

- Folder project tidak berada di `htdocs/baca-di-teras`.
- Nama folder berbeda dari `baca-di-teras`.
- `BASE_URL` dan `RewriteBase` tidak cocok.

Cek file:

```text
index.php
.htaccess
```

Jika folder project tetap `baca-di-teras`, setting yang benar:

```php
define('BASE_URL', '/baca-di-teras');
```

```apache
RewriteBase /baca-di-teras/
```

### `php: command not found`

Gunakan binary XAMPP langsung:

```bash
/Applications/XAMPP/xamppfiles/bin/php -v
```

### `Unknown column 'sor_id'`

Jangan import SQL source sekolah langsung ke `bacaditeras`.

Yang benar:

- Import SQL sekolah ke staging database.
- Jalankan `custom/scripts/migrate_data.php`.

Script akan mapping ke struktur SLiMS 9 dan memakai kolom `sor`, bukan `sor_id`.

### `Database staging ... belum tersedia`

Buat dan import staging database:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "CREATE DATABASE temp_sdbanjarsari CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
/Applications/XAMPP/xamppfiles/bin/mysql -uroot temp_sdbanjarsari < custom/databases/sdbanjarsari.sql
```

Ulangi untuk source lain.

### Data katalog hanya sedikit

Cek apakah dump terbaru sudah diimport:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -uroot -e "SELECT COUNT(*) FROM bacaditeras.biblio WHERE biblio_id >= 100000;"
```

Jika hasilnya bukan sekitar `5248`, import ulang `custom/databases/bacaditeras.sql` atau jalankan migrasi ulang.

### `Illegal mix of collations`

Beberapa tabel core SLiMS masih memakai `utf8_unicode_ci`, sedangkan custom table memakai `utf8mb4_unicode_ci`.

Saat query manual join `item.location_id` ke `bdt_library.slims_location_id`, gunakan:

```sql
ON i.location_id = CONVERT(l.slims_location_id USING utf8) COLLATE utf8_unicode_ci
```

## 9. Catatan Penting

- `bdt_migration_map` adalah audit trail utama untuk melihat asal data.
- `item.location_id` adalah lokasi/source fisik koleksi.
- `biblio.source` adalah source bibliografi.
- Offset masih dipakai untuk menjaga primary key tetap unik.
- Script akan stop jika ID source melewati jatah block `100000`.
- Jangan edit SLiMS core untuk migrasi ini.
