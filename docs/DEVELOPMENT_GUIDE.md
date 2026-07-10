# Development Guide

> Version : 1.0.0
> Last Update : July 2026

---

# 1. Introduction

Dokumen ini berisi standar pengembangan aplikasi **Baca Di Teras** yang wajib diikuti oleh seluruh anggota tim.

Tujuan dibuatnya panduan ini adalah:

- Menjaga konsistensi kode.
- Meminimalisir konflik saat merge Git.
- Mempermudah maintenance.
- Mempermudah onboarding anggota baru.
- Memisahkan pengembangan custom dengan SLiMS Core.

---

# 2. Project Overview

**Baca Di Teras** merupakan portal literasi Desa Teras yang mengintegrasikan:

- Company Profile Desa
- Portal Informasi Desa
- Portal 6 Perpustakaan Desa
- SLiMS sebagai Library Management System
- Integrasi Indonesia OneSearch

---

# 3. Development Principle

Seluruh developer wajib mengikuti prinsip berikut.

## 3.1 Separation of Concern

Pengembangan dibagi menjadi dua bagian.

```
SLiMS Core
+
Custom Layer
```

SLiMS digunakan sebagai engine perpustakaan.

Semua fitur baru dibuat pada layer custom.

---

## 3.2 Core First

Sebisa mungkin jangan mengubah source code bawaan SLiMS.

Jika terdapat kebutuhan fitur baru maka:

✅ Tambahkan file baru

❌ Jangan mengubah banyak file bawaan

---

## 3.3 Reusable Component

Setiap komponen UI dibuat reusable.

Contoh:

- Navbar
- Footer
- Hero
- Card
- Modal
- Button

---

## 3.4 Single Responsibility

Satu file hanya memiliki satu tanggung jawab.

Contoh:

```
navbar.php

footer.php

hero.php
```

Jangan membuat satu file berisi seluruh halaman.

---

# 4. Project Structure

```
baca-di-teras/

docs/

slims/

custom/

assets/

README.md
```

---

## docs/

Berisi dokumentasi proyek.

---

## slims/

Berisi source code SLiMS.

Folder ini merupakan CORE PROJECT.

---

## custom/

Seluruh pengembangan dilakukan di folder ini.

```
custom/

assets/

components/

pages/

helpers/

services/

config/

routes/
```

---

# 5. Folder Rules

## Boleh Diubah

```
custom/
docs/
README.md
```

---

## Hati-hati

```
slims/template/

slims/opac/

slims/index.php
```

Perubahan harus melalui Pull Request.

---

## Tidak Boleh Diubah

```
slims/lib

slims/syslib

slims/simbio2

slims/install
```

Kecuali atas persetujuan Project Lead.

---

# 6. Development Workflow

```
Issue

↓

Create Branch

↓

Development

↓

Commit

↓

Push

↓

Pull Request

↓

Review

↓

Merge Develop

↓

Testing

↓

Release
```

---

# 7. Coding Standard

Developer wajib mengikuti Coding Standard yang terdapat pada:

```
architecture/coding-standard.md
```

---

# 8. UI Standard

Developer wajib mengikuti:

```
UI_GUIDELINE.md
```

Tidak diperbolehkan membuat komponen yang berbeda-beda.

---

# 9. Database Rules

Semua tabel baru dibuat menggunakan prefix.

Contoh

```
village_

website_

library_
```

Contoh

```
website_news

website_article

website_banner

village_profile

library_information
```

Jangan mengubah tabel inti SLiMS jika tidak diperlukan.

---

# 10. Asset Rules

Semua asset ditempatkan pada

```
custom/assets
```

Struktur

```
css/

js/

images/

icons/

fonts/
```

---

# 11. Naming Convention

Folder

```
lowercase
```

Contoh

```
profile

news

library
```

---

File

```
kebab-case
```

Contoh

```
news-card.php

profile-page.php

library-list.php
```

---

PHP Class

```
PascalCase
```

Contoh

```
NewsController

BookService

VillageHelper
```

---

Function

```
camelCase
```

Contoh

```
getNews()

updateBook()

searchLibrary()
```

---

Variable

```
camelCase
```

Contoh

```
$bookList

$libraryName
```

---

Constant

```
UPPER_CASE
```

Contoh

```
APP_NAME

BASE_URL
```

---

# 12. Component Rules

Setiap halaman disusun dari component.

Contoh

```
Navbar

Hero

Section

Card

Footer
```

Tidak diperbolehkan copy-paste HTML.

---

# 13. Error Handling

Semua validasi dilakukan sebelum data diproses.

Contoh

- Empty Data
- Invalid Input
- SQL Error
- Authentication Error

---

# 14. Logging

Semua error penting wajib dicatat.

Contoh

- Login Failed
- Database Error
- API Error

---

# 15. Documentation

Setiap fitur baru wajib memiliki:

- Issue
- Pull Request
- Screenshot
- Deskripsi

---

# 16. Testing

Sebelum Pull Request

Developer wajib memastikan:

- Tidak ada syntax error
- Tidak ada warning
- Tidak ada console error
- Responsive
- Berjalan di localhost

---

# 17. Pull Request Checklist

Sebelum melakukan PR.

- [ ] Feature selesai
- [ ] Tidak ada conflict
- [ ] Sudah pull develop terbaru
- [ ] Build berhasil
- [ ] Screenshot disertakan
- [ ] Documentation diperbarui

---

# 18. Code Review

Reviewer akan memeriksa:

- Struktur kode
- Naming
- UI
- Performance
- Security
- Dokumentasi

Jika belum sesuai maka Pull Request akan ditolak.

---

# 19. Security

Developer dilarang:

- Menyimpan password di source code.
- Menyimpan API Key pada repository.
- Menggunakan query SQL tanpa validasi.
- Mengunggah file sensitif.

Gunakan environment/configuration untuk data sensitif.

---

# 20. Performance

Prioritaskan:

- Reusable component
- Lazy loading asset
- Minify CSS & JS saat production
- Optimasi gambar
- Hindari query database berulang

---

# 21. Git Rules

Ikuti aturan pada:

```
GIT_WORKFLOW.md
```

---

# 22. Branch Rules

Ikuti aturan pada:

```
architecture/branching.md
```

---

# 23. Documentation Rules

Jika terdapat perubahan pada:

- Database
- API
- Folder
- Workflow

Developer wajib memperbarui dokumentasi terkait.

---

# 24. Team Responsibility

Project Lead

- Review Pull Request
- Approve Merge
- Release

Developer

- Mengembangkan fitur
- Membuat dokumentasi
- Testing

UI/UX

- Menjaga konsistensi desain

QA

- Melakukan pengujian aplikasi

---

# 25. Project Goal

Baca Di Teras dikembangkan agar:

- Mudah dikembangkan
- Mudah dipelihara
- Minim konflik Git
- Modular
- Reusable
- Siap dikembangkan pada versi berikutnya