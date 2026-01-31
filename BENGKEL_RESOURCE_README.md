# BengkelResource - Dokumentasi Implementasi

## Overview
Resource Filament untuk mengelola data bengkel dengan fitur:
- ✅ Form bertahap untuk alamat wilayah Indonesia (Provinsi → Kabupaten/Kota → Kecamatan → Desa)
- ✅ Relasi hasMany ke kontak bengkel (1 bengkel bisa punya banyak kontak)
- ✅ Integrasi dengan tabel wilayah Indonesia
- ✅ Menyimpan nama wilayah (string) bukan ID
- ✅ Tampilan yang lebih informatif dan user-friendly

## File yang Dibuat/Dimodifikasi

### 1. Model Wilayah (`app/Models/Wilayah.php`)
- Model untuk tabel wilayah Indonesia
- Memiliki struktur: id, kode, nama, level (provinsi/kabupaten/kecamatan/desa), parent_id
- Scope methods untuk filter berdasarkan level
- **Hanya digunakan sebagai sumber data dropdown, tidak ada relasi langsung**

### 2. Model Bengkel (`app/Models/Bengkel.php`)
- Menyimpan **nama wilayah (string)** bukan ID pada kolom: provinsi, kab_kota, kecamatan, desa
- Accessor `alamat_lengkap` untuk mendapatkan alamat lengkap gabungan
- Relasi hasMany ke KontakBengkel

### 3. BengkelResource (`app/Filament/Resources/BengkelResource.php`)
**Form Features:**
- Section "Informasi Bengkel": Nama dan keterangan
- Section "Alamat Lengkap": 
  - Cascading dropdown (provinsi → kab/kota → kecamatan → desa)
  - Searchable select untuk setiap level
  - Auto-clear field dibawahnya ketika level atas berubah
  - Textarea untuk alamat detail
  - Link Google Maps dengan validasi URL
- Section "Kontak Bengkel":
  - Repeater field untuk menambah multiple kontak
  - Button "Tambah Kontak" untuk menambah kontak baru
  - Field: nama dan nomor telepon
  - Dapat di-collapse per item kontak
  - Section collapsed by default

**Table Features:**
- Menampilkan nama bengkel, provinsi, kab/kota, alamat (langsung dari field)
- Badge untuk jumlah kontak
- Icon untuk Google Maps
- Action button "Buka Maps" untuk langsung membuka Google Maps
- Filter berdasarkan provinsi
- Sortable dan searchable columns
- Tabs untuk filter (Semua, Dengan Maps, Tanpa Maps)

### 4. ViewBengkel Page (`app/Filament/Resources/BengkelResource/Pages/ViewBengkel.php`)
- Infolist dengan sections untuk tampilan detail
- Menampilkan nama wilayah dengan icon
- Link Google Maps yang bisa diklik
- RepeatableEntry untuk menampilkan daftar kontak (collapsible)
- Section collapsed untuk informasi sistem

### 5. ListBengkels Page (`app/Filament/Resources/BengkelResource/Pages/ListBengkels.php`)
- Tabs untuk filter data (Semua, Dengan Google Maps, Tanpa Google Maps)
- Badge counter pada setiap tab
- Action button create dengan icon

## Cara Penggunaan

### Menambah Bengkel Baru:
1. Klik "Create" di halaman list bengkel
2. **Section Informasi Bengkel:**
   - Isi nama bengkel dan keterangan (opsional)
3. **Section Alamat Lengkap:**
   - Pilih wilayah secara bertahap:
     - Pilih Provinsi → dropdown Kab/Kota akan aktif
     - Pilih Kab/Kota → dropdown Kecamatan akan aktif
     - Pilih Kecamatan → dropdown Desa akan aktif
     - Pilih Desa
   - Masukkan alamat detail (jalan, nomor, RT/RW)
   - Opsional: Masukkan link Google Maps
4. **Section Kontak Bengkel (opsional):**
   - Klik "Tambah Kontak" untuk menambah kontak pertama
   - Isi nama dan nomor telepon
   - Klik "Tambah Kontak" lagi jika ingin menambah kontak kedua, ketiga, dst
   - Hapus kontak dengan tombol delete di setiap item
5. Klik "Create"

### Mengedit Bengkel:
1. Buka halaman edit bengkel
2. Ubah data yang diperlukan
3. Untuk kontak:
   - Tambah kontak baru dengan "Tambah Kontak"
   - Edit kontak yang sudah ada
   - Hapus kontak dengan tombol delete
4. Klik "Save"

## Struktur Tabel Wilayah
```
wilayah
├── id (bigint, PK)
├── kode (varchar 20, unique)
├── nama (varchar 255)
├── level (enum: provinsi, kabupaten, kecamatan, desa)
└── parent_id (bigint, FK ke wilayah.id)
```

## Tips
- **Data disimpan sebagai STRING (nama wilayah)**, bukan sebagai ID
- **Kontak ditambahkan langsung di form**, tidak menggunakan relation manager terpisah
- Repeater kontak: klik "Tambah Kontak" untuk menambah lebih banyak
- Section kontak collapsed by default, expand jika perlu menambah kontak
- Cascading dropdown tetap menggunakan data dari tabel wilayah untuk konsistensi
- Cascading dropdown otomatis reset nilai dibawahnya saat nilai atasnya berubah
- Semua dropdown wilayah bersifat searchable untuk memudahkan pencarian
- Link Google Maps otomatis menambahkan prefix "https://"
- Tampilan table responsive dengan toggleable columns
- Gunakan tabs untuk filter cepat bengkel dengan/tanpa Google Maps
- Button "Buka Maps" hanya muncul jika bengkel memiliki link Google Maps

## Struktur Form (Flow)
```
📋 Form Create/Edit Bengkel
│
├─ 📑 Section: Informasi Bengkel (expanded)
│  ├─ Nama Bengkel *required
│  └─ Keterangan
│
├─ 📍 Section: Alamat Lengkap (expanded)
│  ├─ Provinsi *required → Searchable Select
│  ├─ Kab/Kota *required → Auto-enabled after Provinsi selected
│  ├─ Kecamatan *required → Auto-enabled after Kab/Kota selected
│  ├─ Desa *required → Auto-enabled after Kecamatan selected
│  ├─ Alamat Detail *required → Textarea
│  └─ Link Google Maps → URL input
│
└─ 👥 Section: Kontak Bengkel (collapsed)
   └─ Repeater: kontakBengkels
      ├─ [Tambah Kontak] Button
      └─ Items:
         ├─ Nama Kontak *required
         └─ Nomor Telepon *required
```
