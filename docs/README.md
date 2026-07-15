# 📚 Baca Di Teras Documentation

Folder ini berisi seluruh dokumentasi teknis proyek **Baca Di Teras**, yaitu portal informasi dan perpustakaan digital Desa Teras yang dibangun menggunakan SLiMS sebagai library management engine.

## 📂 Struktur Dokumentasi

| File | Deskripsi |
|------|-----------|
| SRS.md | Software Requirements Specification. Berisi kebutuhan fungsional dan nonfungsional sistem. |
| DEVELOPMENT_GUIDE.md | Standar pengembangan aplikasi, struktur folder, coding style, dan aturan modifikasi proyek. |
| UI_GUIDELINE.md | Panduan desain antarmuka, komponen, warna, tipografi, spacing, dan layout. |
| GIT_WORKFLOW.md | Aturan penggunaan Git, branching strategy, pull request, code review, dan commit convention. |
| DATABASE.md | Dokumentasi database, ERD, tabel tambahan, serta relasi dengan database SLiMS. |
| DATA_MIGRATION.md | Rencana dan implementasi migrasi data 4 perpustakaan sekolah, termasuk source entity, offset, mapping, dan verifikasi. |
| RUN_AND_MIGRATION_GUIDE.md | Panduan operasional untuk menjalankan aplikasi, import database, menjalankan migrasi, update dump SQL, dan troubleshooting. |
| API.md | Dokumentasi API internal, endpoint, format request/response, serta integrasi Indonesia OneSearch. |

---

## 🏗️ Arsitektur Sistem

Project dibagi menjadi dua bagian utama.

### 1. SLiMS Core

SLiMS digunakan sebagai engine utama pengelolaan perpustakaan.

Folder core sebisa mungkin **tidak dimodifikasi** agar memudahkan proses update di masa depan.

### 2. Custom Layer

Semua pengembangan Baca Di Teras ditempatkan pada folder `custom/`, meliputi:

- Company Profile Desa
- Homepage
- Berita
- Artikel
- Informasi
- Halaman Perpustakaan
- Integrasi Indonesia OneSearch
- Komponen UI

---

## 📌 Prinsip Pengembangan

- Jangan mengubah SLiMS Core tanpa persetujuan tim.
- Semua fitur baru dikembangkan pada layer `custom`.
- Setiap perubahan dilakukan melalui Pull Request.
- Seluruh commit mengikuti Conventional Commits.
- Setiap fitur harus memiliki Issue sebelum mulai dikembangkan.

---

## 📖 Urutan Membaca Dokumentasi

1. DEVELOPMENT_GUIDE.md
2. GIT_WORKFLOW.md
3. UI_GUIDELINE.md
4. DATABASE.md
5. DATA_MIGRATION.md
6. RUN_AND_MIGRATION_GUIDE.md
7. API.md
8. SRS.md

---

## 🚀 Tujuan Proyek

Baca Di Teras merupakan portal literasi Desa Teras yang mengintegrasikan:

- Profil Desa
- Enam perpustakaan desa
- Katalog buku berbasis SLiMS
- Berita dan artikel
- Informasi layanan perpustakaan
- Integrasi Indonesia OneSearch
