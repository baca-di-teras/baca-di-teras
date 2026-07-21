# 📚 Baca Di Teras

> Portal Literasi & Perpustakaan Digital Desa Teras

**Baca Di Teras** adalah portal informasi dan perpustakaan digital yang mengintegrasikan profil Desa Teras dengan enam perpustakaan desa. Portal ini menyediakan katalog buku terpadu, berita, artikel, serta informasi layanan perpustakaan bagi masyarakat Desa Teras.

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 🏠 **Landing Page** | Halaman utama dengan hero, koleksi terbaru, daftar perpustakaan, dan berita terkini |
| 🏘️ **Profil Desa** | Sejarah, visi & misi, struktur organisasi, dan potensi Desa Teras |
| 📖 **Perpustakaan** | Direktori 6 perpustakaan desa beserta detail, lokasi, dan fasilitas masing-masing |
| 📕 **Katalog Buku** | Pencarian & filter buku dari seluruh perpustakaan dengan detail bibliografi lengkap |
| 📰 **Berita** | Portal berita kegiatan desa dan perpustakaan |
| ✍️ **Artikel** | Artikel literasi dan informasi edukatif |
| ℹ️ **Informasi** | Panduan layanan, tata tertib, FAQ, dan jam operasional perpustakaan |
| 📞 **Kontak** | Informasi kontak dan formulir pesan |
| 🔑 **Admin Panel** | Pengelolaan konten melalui panel admin |

---

## 🏗️ Arsitektur Sistem

Proyek dibangun menggunakan **SLiMS (Senayan Library Management System)** sebagai engine pengelolaan perpustakaan, dikombinasikan dengan **Custom Layer** untuk portal informasi desa.

```
┌──────────────────────────────────────────────┐
│               Baca Di Teras                  │
│                                              │
│  ┌────────────────┐  ┌────────────────────┐  │
│  │   SLiMS Core   │  │   Custom Layer     │  │
│  │                │  │                    │  │
│  │  • Manajemen   │  │  • Landing Page    │  │
│  │    Perpustakaan│  │  • Profil Desa     │  │
│  │  • Katalog     │  │  • Perpustakaan    │  │
│  │  • Sirkulasi   │  │  • Berita/Artikel  │  │
│  │  • Admin       │  │  • Informasi       │  │
│  │  • OAI-PMH     │  │  • Kontak          │  │
│  └────────────────┘  └────────────────────┘  │
└──────────────────────────────────────────────┘
```

---

## 🛠️ Dibangun Dengan

| Komponen | Teknologi |
|----------|-----------|
| **Backend** | PHP |
| **Library Engine** | [SLiMS 9 Bulian](https://slims.web.id/) |
| **Database** | MySQL / MariaDB |
| **Web Server** | Apache |

---

## 👥 Role & Hak Akses

### Super Admin
- Mengelola seluruh sistem, akun admin, profil website, dan konfigurasi
- Mengelola seluruh koleksi buku dari semua perpustakaan
- Mengelola berita, artikel, dan halaman informasi
- Backup & restore data sistem

### Admin Perpustakaan
- Mengelola koleksi buku pada perpustakaan yang menjadi tanggung jawabnya
- Mengelola data bibliografi, eksemplar, dan metadata buku
- Mengelola berita dan kegiatan perpustakaan

### Pengguna (Publik)
- Mengakses seluruh informasi tanpa login
- Mencari dan menelusuri katalog buku
- Membaca berita, artikel, dan informasi layanan

---

## 🗺️ Halaman

| Halaman | Deskripsi |
|---------|-----------|
| **Beranda** | Halaman utama portal |
| **Profil Desa** | Informasi lengkap tentang Desa Teras |
| **Perpustakaan** | Daftar & detail 6 perpustakaan desa |
| **Katalog Buku** | Pencarian & detail koleksi buku |
| **Berita** | Berita terkini seputar desa & perpustakaan |
| **Artikel** | Artikel literasi & edukatif |
| **Informasi** | Panduan layanan, tata tertib, dan FAQ |
| **Kontak** | Informasi kontak & formulir pesan |

---

## 📜 Lisensi

Proyek ini menggunakan SLiMS yang dilisensikan di bawah [GNU General Public License v3.0](slims/LICENSE).

---

## 🙏 Acknowledgements

- [SLiMS (Senayan Library Management System)](https://slims.web.id/) — Open source library management system
- [Indonesia OneSearch](https://onesearch.id/) — Integrasi katalog nasional
- Pemerintah Desa Teras — Inisiator portal literasi desa

---

<p align="center">
  Dibuat dengan ❤️ oleh Tim <strong>Baca Di Teras</strong>
</p>