# Git Workflow

> Version : 1.0.0  
> Last Update : July 2026

---

# 1. Tujuan

Dokumen ini menjelaskan standar penggunaan Git pada proyek **Baca Di Teras**.

Tujuan dibuatnya workflow ini adalah:

- Menghindari konflik merge.
- Memudahkan kolaborasi antar developer.
- Menjaga stabilitas branch utama.
- Memudahkan tracking perubahan kode.
- Menjaga kualitas source code sebelum masuk ke repository utama.

---

# 2. Branch Strategy

Project menggunakan **Git Flow** sederhana.

```
main
│
├── develop
│
├── feature/*
│
├── fix/*
│
├── hotfix/*
│
└── release/*
```

## Branch Description

| Branch | Fungsi |
|---------|--------|
| `main` | Production branch. Selalu berisi kode yang stabil dan siap digunakan. |
| `develop` | Branch integrasi seluruh fitur sebelum dirilis. |
| `feature/*` | Branch pengembangan fitur baru. |
| `fix/*` | Branch perbaikan bug yang belum masuk production. |
| `hotfix/*` | Perbaikan bug pada production. |
| `release/*` | Persiapan sebelum release versi baru. |

---

# 3. Branch Rules

### main

- Tidak boleh langsung di-push.
- Hanya Project Lead yang dapat melakukan merge.
- Harus selalu dalam kondisi stabil.

---

### develop

Semua feature akan digabung ke branch ini.

Developer wajib:

- Pull terbaru sebelum mulai bekerja.
- Resolve conflict sebelum membuat Pull Request.

---

### feature

Setiap fitur memiliki branch sendiri.

Contoh

```
feature/homepage

feature/profile-desa

feature/news

feature/article

feature/library

feature/navbar

feature/information

feature/search-book
```

---

### fix

Untuk bug kecil.

Contoh

```
fix/login

fix/navbar

fix/search
```

---

### hotfix

Untuk bug yang sudah berada di production.

Contoh

```
hotfix/login-error

hotfix/security

hotfix/database
```

---

# 4. Workflow Development

```
Issue

↓

Assign Developer

↓

Create Feature Branch

↓

Development

↓

Commit

↓

Push

↓

Pull Request

↓

Code Review

↓

Revision (Jika Ada)

↓

Merge ke Develop

↓

Testing

↓

Merge ke Main
```

---

# 5. Membuat Branch Baru

Pastikan branch develop sudah paling baru.

```
git checkout develop

git pull origin develop
```

Membuat branch baru.

```
git checkout -b feature/profile-desa
```

---

# 6. Commit Convention

Project menggunakan **Conventional Commit**.

Format

```
type(scope): description
```

Contoh

```
feat(home): add hero section

feat(news): create article page

fix(login): fix authentication

refactor(navbar): simplify menu generation

docs(readme): update installation guide

style(button): adjust padding

test(search): add search unit test

chore(deps): update composer packages
```

---

# 7. Commit Type

| Type | Keterangan |
|------|------------|
| feat | Menambahkan fitur baru |
| fix | Memperbaiki bug |
| docs | Perubahan dokumentasi |
| style | Perubahan tampilan tanpa mengubah logika |
| refactor | Perubahan struktur kode tanpa mengubah fungsi |
| test | Penambahan atau perubahan testing |
| chore | Maintenance, dependency, konfigurasi |

---

# 8. Pull Request

Setelah fitur selesai.

```
feature/profile-desa

↓

Pull Request

↓

develop
```

Jangan membuat Pull Request langsung ke `main`.

---

# 9. Pull Request Template

Judul

```
feat(profile): add profile desa page
```

Deskripsi

```
## Deskripsi

Menambahkan halaman Profil Desa.

## Perubahan

- Hero
- Sejarah
- Visi Misi
- Struktur Organisasi

## Screenshot

(lampirkan)

## Checklist

- [ ] Build berhasil
- [ ] Tidak ada error
- [ ] Responsive
- [ ] Documentation diperbarui
```

---

# 10. Code Review

Minimal terdapat **1 reviewer** sebelum merge.

Reviewer memeriksa:

- Struktur kode
- Naming convention
- UI sesuai guideline
- Potensi bug
- Dokumentasi
- Keamanan dasar

---

# 11. Merge Rules

Merge hanya dilakukan jika:

- Pull Request telah disetujui.
- Tidak ada conflict.
- Semua checklist terpenuhi.
- Dokumentasi telah diperbarui (jika diperlukan).

---

# 12. Sync Branch

Sebelum mulai bekerja.

```
git checkout develop

git pull origin develop
```

Masuk ke feature.

```
git checkout feature/profile-desa
```

Update branch.

```
git merge develop
```

atau

```
git rebase develop
```

Gunakan salah satu sesuai kesepakatan tim.

---

# 13. Conflict Resolution

Jika terjadi conflict.

1. Pull branch terbaru.
2. Resolve conflict.
3. Jalankan aplikasi.
4. Pastikan tidak ada error.
5. Commit hasil resolve.
6. Push kembali.

Developer **tidak boleh** melakukan merge jika conflict belum diselesaikan.

---

# 14. Issue Workflow

Setiap pekerjaan harus berasal dari Issue.

Contoh

```
#12

Membuat halaman Profil Desa
```

Issue berisi:

- Deskripsi
- Acceptance Criteria
- Penanggung jawab
- Label

---

# 15. Labels

Gunakan label berikut.

| Label | Fungsi |
|--------|--------|
| feature | Fitur baru |
| bug | Bug |
| enhancement | Peningkatan |
| documentation | Dokumentasi |
| ui | UI/UX |
| backend | Backend |
| frontend | Frontend |
| database | Database |
| api | API |
| urgent | Prioritas tinggi |

---

# 16. Versioning

Project menggunakan Semantic Versioning.

```
v1.0.0
```

Format

```
Major.Minor.Patch
```

Contoh

```
1.0.0

↓

1.1.0

↓

1.2.0

↓

2.0.0
```

---

# 17. Release Workflow

```
develop

↓

release/v1.0.0

↓

Testing

↓

main

↓

Tag

↓

Deploy
```

---

# 18. Git Ignore

File berikut tidak boleh di-commit.

```
.env

vendor/

node_modules/

.idea/

.vscode/

.DS_Store/

Thumbs.db

*.log
```

Ikuti file `.gitignore` yang ada pada repository.

---

# 19. Best Practices

✅ Pull sebelum mulai bekerja.

✅ Buat branch baru untuk setiap fitur.

✅ Commit kecil tetapi jelas.

✅ Gunakan pesan commit yang deskriptif.

✅ Selesaikan satu fitur sebelum berpindah ke fitur lain.

✅ Lakukan testing sebelum membuat Pull Request.

✅ Perbarui dokumentasi jika ada perubahan arsitektur atau database.

---

# 20. Hal yang Tidak Diperbolehkan

❌ Push langsung ke `main`.

❌ Commit banyak fitur berbeda dalam satu commit.

❌ Mengubah SLiMS Core tanpa persetujuan Project Lead.

❌ Menghapus branch orang lain.

❌ Force push ke branch bersama (`develop` atau `main`).

❌ Merge tanpa proses code review.

---

# 21. Alur Git Proyek Baca Di Teras

```text
main
│
└── develop
    │
    ├── feature/homepage
    ├── feature/profile-desa
    ├── feature/perpustakaan
    ├── feature/katalog
    ├── feature/berita
    ├── feature/artikel
    ├── feature/informasi
    ├── feature/contact
    ├── feature/ios
    └── feature/admin-custom
```

---

# 22. Penanggung Jawab

**Project Lead**

- Menyetujui Pull Request.
- Melakukan merge ke `develop` dan `main`.
- Menentukan jadwal release.

**Developer**

- Mengembangkan fitur sesuai Issue.
- Mengikuti standar coding.
- Membuat dokumentasi jika diperlukan.
- Melakukan testing sebelum Pull Request.

---

# 23. Referensi

- Git Documentation
- Conventional Commits
- Semantic Versioning (SemVer)

---

> **Catatan:** Seluruh anggota tim wajib mengikuti workflow ini selama proses pengembangan. Apabila diperlukan perubahan pada workflow, perubahan harus disepakati oleh Project Lead dan seluruh anggota tim.